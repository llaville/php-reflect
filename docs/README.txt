HOW TO build yourself The User Guide written for AsciiDoc

NOTE: You should have installed on your system
.For standard HTML or Docbook targets

AsciiDoc 8.6.8
    http://www.methods.co.nz/asciidoc/
Source-Highlight 3.1+
    http://www.gnu.org/software/src-highlite/
or
Pygments 1.5+
    http://pygments.org/

.For PDF target
Apache FOP
    http://xmlgraphics.apache.org/fop/index.html

With basic layout, and linked javascript and styles
$ asciidoc-8.6.8/asciidoc.py
  -a icons
  -a toc2
  -a linkcss
  -a theme=flask
  -n
  -v
  docs/phpreflect-book.txt

With basic layout, and embbeded javascript and styles
$ asciidoc-8.6.8/asciidoc.py
  -a icons
  -a toc2
  -a theme=flask
  -n
  -v
  docs/phpreflect-book.txt

Or used Phing 2.4.12

But be careful to change first properties 'asciidoc.home' and 'homedir' values 
that reflect your platform and installation.

Since version 1.2.0 you can use alternative solution: use a properties file that define
all values you wan't to overload (example)

phing  /path/to/builddocs.xml -Ddefault.properties=/path/to/your-local.properties

Single Html file
phing  -f /path/to/builddocs.xml  make-userguide-html

Many Html files
phing  -f /path/to/builddocs.xml  make-userguide-chunked

Microsoft Html Help file (chm format)
phing  -f /path/to/builddocs.xml  make-userguide-htmlhelp

PDF file (with FOP) A4 format
phing  -f /path/to/builddocs.xml  make-userguide-pdf-a4

PDF file (with FOP) US format
phing  -f /path/to/builddocs.xml  make-userguide-pdf-us

EPUB file
phing  -f /path/to/builddocs.xml  make-userguide-epub

All files format build on the same run
phing  -f /path/to/builddocs.xml  make-userguide-all

See script docs/build-phing.xml if you want to build all parts of documentation
