#! /usr/bin/env python

"""Fix ugly file names to be UNIX shell-friendly.

PROBLEM
=======

You have files named with funky characters lying around in your
filesystem.  Ugly files like "My Document #3 - (2005)[1].txt" are
common when you're sharing directories with Windows users, but you
would like to have them renamed to something like
"my_document_3_-_2005-1-.txt" so that your shell and other unix
utilities won't have to deal with special characters.


DISCUSSION
==========

Three file name manipulations are performed.

- Applies a string function such as upper, lower, or capwords.

- Converts characters via user-specified translation options.

- Removes shell-unfriendly characters.

The removal candidate characters are selected by creating a set of all
punctuation characters and subtracting a set of shell-compatible
characters.  After all ugly files have been found using walk and
glob, their paths are sent to beautify to fix the name, excluding
the path and extension.

Flexibility is offered by various optparse command-line options,
such as directory-recursion, dryrun mode, silent mode, string function
application, character translation and deletion, and file type
exclusion.

The available functionality is best viewed by running::

    $ fnfix.py --help

The following is an example usage saying: translate all spaces into
underscores and all parentheses into dashes, and recurse into
subdirectories, and show the change operations, but don't actually
perform the renaming::

    $ fnfix.py -nr -f' ()' -t'_--' /var/mp3/jazz

This recipe features the use of Python's string.translate, optparse,
set, doctest, os.walk, file/path manipulation with os.path and glob,
and list comprehensions.

This recipe could be improved/expanded by adding support for regex
pattern matching of file name limiting, or could offer inverse of
limit -- say INCLUDEGLOB or INCLUDEPATTERN.  More error checking could
be done.  It could accept multiple directory arguments.  Use of set
requires Python 2.4.  A character squeeze feature could be useful.


SEE ALSO
========

There are many MP3-centric utilities that offer bulk file name
conversion.  Unix manpages for tr(1) has similar concepts, and bash(1)
describes shell special characters.

You can use `pathchk(1)` to verify suitability of portable file names.

Keywords: tr, unix, shell, system administration

"""

__author__ = "Micah Elliott http://MicahElliott.com"

import glob, os, string, sys, optparse

USAGE = '%prog [OPTS] UGLYDIR'
DESCR = __doc__.split('\n\n')[0]

# Subtract the acceptable funky chars from all punctuation chars.
UGLYCHARS = ''.join( set(string.punctuation+' ') - set('-_+.~%') )

#---------------------------------------------------------------------

def split_dir_base_ext(path):
    """Split a path into a 3-tuple containing dirname, filename sans
    extension, and extension.

    :param: path  this gets split

    >>> split_dir_base_ext('/foo/bar/biz.txt')
    ('/foo/bar', 'biz', '.txt')

    #>>> split_dir_base_ext('.txt')
    #('', '', '.txt')
    """
    dn = os.path.dirname(path)
    name, ext = os.path.splitext(os.path.basename(path))
    return dn, name, ext

def beautify(uglypath, table, delchars='', stringfunc=None):
    """Make three changes to a name in an ugly path.
    The changes are (1) apply a string function, (2) translate
    characters, and (3) delete characters.
    >>> table = string.maketrans('', '')
    >>> beautify('/foo/bar/a"b)c]d e.txt', table, UGLYCHARS)
    '/foo/bar/abcde.txt'
    >>> beautify("03 - Blue 'n' Boogie.mp3", table, UGLYCHARS)
    '03-BluenBoogie.mp3'
    >>> beautify("My Document #3 - (2005)[1].txt", table, UGLYCHARS)
    'MyDocument3-20051.txt'
    >>> beautify('a_b-c', table, UGLYCHARS, string.upper)
    'A_B-C'
    """
    dirname, ugly2pretty, ext = split_dir_base_ext(uglypath)
    if stringfunc is not None:
        ugly2pretty = stringfunc(ugly2pretty)
    # Translate FROMCHARS to TOCHARS and delete DELCHARS
    ugly2pretty = ugly2pretty.translate(table, delchars)
    return os.path.join(dirname, ugly2pretty+ext)

def fix_ugly_names(opts, uglydir):
    """Rename ugly file names to a beautified shell-correct names.
    Collect ugly file names, perform beautification, rename.
    """
    ### Shell-unfriendly characters made into a string.
    # The user-provided FROMCHARS and TOCHARS must be removed from the
    # UGLYCHARS so that they can be transformed instead of removed.
    delchars = opts.delchars + ''.join(set(UGLYCHARS)
                                       - set(opts.tochars+opts.fromchars))
    ### Table for later translation (removal of `delchars`).
    table = string.maketrans(opts.fromchars, opts.tochars)
    uglyroot = os.path.expanduser(uglydir)
    ### Build list of name-change candidates.
    if opts.recurse:
        uglies = [f
            for root , _, _ in os.walk(uglyroot, topdown=False)
                for f in glob.glob(os.path.join(root, opts.limitglob))]
    else:
        uglies = glob.glob(os.path.join(uglyroot, opts.limitglob))
    pretties = [beautify(ugly, table, delchars, opts.stringfunc)
        for ugly in uglies]
    ### Do the renaming.
    for ugly, pretty in zip(uglies, pretties):
        if ugly != pretty:
            if not opts.silent: print ugly, '-->', pretty
            if not opts.dryrun: os.rename(ugly, pretty)

def error_checks(cmdln, args, opts):
    """Ensure proper user input.
    """
    ###print args
    #print args
    if len(args) != 1:
        cmdln.error('Must specify *one* UGLYDIR.')
    if not os.path.isdir(args[0]):
        cmdln.error('UGLYDIR must be a directory.')
    for c in opts.tochars:
        if c in UGLYCHARS:
            cmdln.error('TOCHARS contained ugly character "%s".' % c)
    if (bool(opts.fromchars) + bool(opts.tochars)) % 2 != 0:
        cmdln.error('-t and -f must be used together.')
    if len(opts.fromchars) != len(opts.tochars):
        cmdln.error('FROMCHARS and TOCHARS must be of equal length.')

def cmdln():
    """Setup command line parser.
    """
    cmdln = optparse.OptionParser(usage=USAGE, description=DESCR)
    cmdln.add_option('-r', dest='recurse', action='store_true',
                     help='Recurse into subdirs')
    cmdln.add_option('-s', dest='silent', action='store_true',
                     help='Silent mode')
    cmdln.add_option('-n', dest='dryrun', action='store_true',
                     help='dry run/No-op mode (don\'t actually rename)')
    cmdln.add_option('-L', dest='lower', action='store_true',
                     help='make Lower case (string.lower)')
    cmdln.add_option('-U', dest='upper', action='store_true',
                     help='make Upper case (string.upper)')
    cmdln.add_option('-C', dest='capwords', action='store_true',
                     help='Capitalize words (string.capwords)')
    cmdln.add_option('-f', dest='fromchars', default='',
                     help='translate From FROMCHARS characters (requires -t)')
    cmdln.add_option('-t', dest='tochars', default='',
                     help='translate To TOCHARS characters (requires -f)')
    cmdln.add_option('-d', dest='delchars', default='',
                     help='Delete DELCHARS characters from file names')
    cmdln.add_option('-l', dest='limitglob', default='*',
                     help='Limit file globbing to LIMITGLOB pattern')
    opts, args = cmdln.parse_args(sys.argv[1:])
    opts.stringfunc = lambda x: x
    if opts.capwords: opts.stringfunc = string.capwords
    if opts.upper:    opts.stringfunc = string.upper
    if opts.lower:    opts.stringfunc = string.lower
    error_checks(cmdln, args, opts)
    return opts, args[0]

######################################################################

if __name__ == '__main__':
    # Run doctest when run as main script.
    import doctest
    doctest.testmod(sys.modules[__name__])
    opts, uglydir = cmdln()
    fix_ugly_names(opts, uglydir)
