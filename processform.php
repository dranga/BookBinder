<?php
#checks for whitespace in filenames
#check files are pdfs
#check no special characters in section names
#if section and/or file empty, ignore
#delete files in tmp

#add option to add blank page after TOC
#add option to return LaTeX instead of pdf



$attachment_dir = "tmp";
$attachment_name = "tempbook";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$attachment_location = "/" . $attachment_dir . "/". $attachment_name . ".tex";
	$attachment_location_pdf = "/" . $attachment_dir . "/". $attachment_name . ".pdf";

	#$file_handle = fopen($attachment_location, "w+") or die("Error: Unable to open or create file");
	#move_uploaded_file($_FILES["inputentryname"]["tmp_name"], $target_file

	if($_FILES["cover"] != NULL) {
		$cover_filename = pathinfo($_FILES["cover"]["name"], PATHINFO_FILENAME);
		move_uploaded_file($_FILES["cover"]["tmp_name"], "/$attachment_dir/{$_FILES["cover"]["name"]}");
	}

	$blankcover = $_POST["blankcover"];

	if($_FILES["back"] != NULL) {
		$back_filename = pathinfo($_FILES["back"]["name"], PATHINFO_FILENAME);
		move_uploaded_file($_FILES["back"]["tmp_name"], "/$attachment_dir/{$_FILES["back"]["name"]}");
	}

	$makeeven = $_POST["makeeven"];

	$TOC = $_POST["TOC"];

	$rightmargin = $_POST["right"];
	$leftmargin = $_POST["left"];
	$topmargin = $_POST["top"];
	$bottommargin = $_POST["bottom"];

	foreach ($_FILES["files"]["error"] as $key => $error) {
    	if ($error == UPLOAD_ERR_OK) {
			$sectionnames_array[$key] = $_POST["sectionnames"][$key];
			$sectionfilenames_array[$key] = pathinfo($_FILES["files"]["name"][$key], PATHINFO_FILENAME);
			move_uploaded_file($_FILES["files"]["tmp_name"][$key], "/$attachment_dir/{$_FILES["files"]["name"][$key]}");
		}
	}

	$file_text = '';

	$file_text .= FileHeader($cover_filename, $blankcover, $TOC, $rightmargin, $leftmargin, $topmargin, $bottommargin);
	for ($i = 0; $i < count($sectionnames_array); $i++) {
		$file_text .= NewSection($sectionnames_array[$i], $sectionfilenames_array[$i]);
	}
	

	$back_text = FileFooter($back_filename, NULL);

	$file_handle = fopen($attachment_location, "w+") or die("Error: Unable to open or create file");
	
	fwrite($file_handle, $file_text . $back_text);
	fclose($file_handle);


	chdir("/tmp");
	exec('latexmk -pdf -pdflatex="pdflatex -interaction=batchmode" ' . $attachment_location . " 2>&1");
	$pages = getPDFPages($attachment_location_pdf);

	#always makes even, nothing is done with the $makeeven variable
	if($makeeven && $pages%2 != 0){
		$back_text = FileFooter($back_filename, "true");

		$file_handle = fopen($attachment_location, "w+") or die("Error: Unable to open or create file");
		
		fwrite($file_handle, $file_text . $back_text);
		fclose($file_handle);
		exec('latexmk -pdf -pdflatex="pdflatex -interaction=batchmode" ' . $attachment_location . " 2>&1");
	}
	/*
	echo "START<br>";
	echo getcwd() . "<br>";
	echo file_exists($attachment_location_pdf) . "+++<br>";
	echo filesize($attachment_location_pdf) . "+++<br>";
	echo "END";
	*/
	#exec('latexmk -pdf -pdflatex="pdflatex -interaction=nonstopmode" ' . $attachment_location);
	#exec('latexmk -pdf -pdflatex="pdflatex -interaction=nonstopmode" ' . $attachment_location);
	
	if (file_exists($attachment_location_pdf)) {
		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
		header("Cache-Control: public"); // needed for i.e.
		header("Content-type:application/pdf");
		#header("Content-Length:".filesize($attachment_location_pdf));
		header("Content-Disposition: attachment; filename=/tmp/tempbook.pdf");
		readfile($attachment_location_pdf);
		#header("Content-Disposition: attachment; filename=\"tempbook.tex\"");
	} else {
            die("Error: File not found.");
    }
}

function FileHeader($cover_filename, $blankcover, $TOC, $right, $left, $top, $bottom)
{
	global $attachment_dir;
	$text = "\\documentclass[twoside, letterpaper]{article}\n" .
			"\\usepackage{pdfpages}\n" .
			"\\usepackage{tocloft}\n" .
			"\\usepackage{geometry}\n\n" .

			"\\usepackage{fancyhdr}\n" .
			"\\pagestyle{fancy}\n" .
			"\\lhead{}\\chead{}\\rhead{}\n" .
			"\\cfoot{}\n" .
			"\\fancyfoot[LE,RO]{\\thepage} \n\n" .

			"\\renewcommand{\\headrulewidth}{0pt}\n" .
			"\\renewcommand\\cftsecleader{\\cftdotfill{\\cftdotsep}} \n" .
			"\\setlength\cftaftertoctitleskip{2cm}\n\n" .

			"\\renewcommand{\\contentsname}{\\sffamily Contents}\n\n" .
			
			"\\begin{document}\n\n"; #document, includes etc

	$text .= "\\includepdf{/$attachment_dir/$cover_filename.pdf}\n"; #coverpage
	
	if($blankcover) {
		$text .= "\\null\\newpage\n\n";
	}

	if($TOC) {
		$text .= "\\newgeometry{top=2in,bottom=1in,right=1.5in,left=1.5in}\n" .
				"{\\large\\sffamily\\tableofcontents}\n\n"; #special TOC margins
	}

	$text .= "\\newgeometry{top={$top}in,bottom={$bottom}in,right={$right}in,left={$left}in}\n\n"; #return to normal margins
	return $text;
}

#\documentclass[twoside, letterpaper]{article}
#\usepackage{pdfpages}
#\usepackage{geometry}
#
#\usepackage{fancyhdr}
#\pagestyle{fancy}
#\lhead{}\chead{}\rhead{}
#\cfoot{}
#\fancyfoot[LE,RO]{\thepage}
#
#\renewcommand{\headrulewidth}{0pt}
#\usepackage{tocloft}
#\renewcommand\cftsecleader{\cftdotfill{\cftdotsep}}
#\setlength\cftaftertoctitleskip{2cm}

#\renewcommand{\contentsname}{\sffamily Contents} 

#\begin{document}

#\includepdf{"filename"}
#\null\newpage % balnk backpage


#\newgeometry{top=2in,bottom=1in,right=1.5in,left=1.5in} 
#{\large\sffamily\tableofcontents}
#\newpage
#\newgeometry{top=1in,bottom=0.75in,right=0.25in,left=2in}


#function NewSection
#
# $section_name: name of the section as it would appear in the TOC, ! no special characters
# $filename: file name, no extention, no path (pdf in same folder)
function NewSection($section_name, $filename)
{
	global $attachment_dir;
	$text = "\\addcontentsline{toc}{section}{{$section_name}}\n" .
			"\\includepdf[pages=-, pagecommand={}]{/$attachment_dir/$filename.pdf}\n\n";
	return $text;
}

# function FileFooter
#
# $backcover_filename: file name of the back cover, no extention, no path (pdf in same folder)
# $makeeven: add a blank page if value is "true"
function FileFooter($backcover_filename, $makeeven)
{
	global $attachment_dir;
	$text = "";
	if($makeeven) {
		$text .= "\\null\\newpage\n";
	}

	$text .= "\\includepdf[pages=-, pagecommand={}]{/$attachment_dir/$backcover_filename.pdf}\n" .
			"\\end{document}\n";

	return $text;
}

#http://stackoverflow.com/questions/14644353/get-the-number-of-pages-in-a-pdf-document
function getPDFPages($document)
{

    // Parse entire output
    // Surround with double quotes if file name has spaces
    global $attachment_dir;
    exec("pdfinfo $document", $output);

    // Iterate through lines
    $pagecount = 0;
    foreach($output as $op)
    {
        // Extract the number
        if(preg_match("/Pages:\s*(\d+)/i", $op, $matches) === 1)
        {
            $pagecount = intval($matches[1]);
            break;
        }
    }

    return $pagecount;
}

?>