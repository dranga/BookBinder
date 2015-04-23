BookBinder
============

BookBinder is a PHP web application to generate custom PDF e-books from PDF sections (front and back covers, and chapters).

BookBinder generates and compiles a LaTeX file. The resulting PDF is returned to the user.

##Features
* Choice of front cover and addition blank back to the cover
* Choice of back cover
* Numbered pages, in book format (right corner for odd pages and in the left corner for even pages)
* Adjustable page number using page margins
* Addition of a table of contents
* Creating an even number to have the back cover as an even page (like a book)

##Limitations
BookBinder throws an error if:
* No front or back cover PDFs are specified
* Section titles contain any special characters ($, &, %, #, _, {, }, ~, ^, \\)
* Upload failure or non-PDF file upload

BookBinder dies on file IO errors.

BookBinder ignores sections with either no section name or file specified.

##Software Dependencies
On a Debian server (Ubuntu 14.04)
* apache2 (and server running)
* php5
* latexmk
* texlive-base
* texlive-latex-base
* texlive-latex-recommended
* texlive-latex-extra
* pdfinfo

##Future Improvements
* Add option to add blank page after TOC
* Add option to return LaTeX instead of pdf