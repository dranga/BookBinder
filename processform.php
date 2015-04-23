<?php

#add option to add blank page after TOC
#add option to return LaTeX instead of pdf

include('helper.php');

$attachment_dir = "/tmp/bookbinder";
$attachment_name = "tempbook";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	if(file_exists($attachment_dir)) {
		deleteDir($attachment_dir);
	}
	if(!file_exists($attachment_dir)) {
		mkdir($attachment_dir);
	}

	//if(!file_exists($attachment_dir)) {
	//	mkdir($attachment_dir); //causes error if folder exists, so no die() statement
	//}

	$attachment_location = $attachment_dir . "/". $attachment_name . ".tex";
	$attachment_location_pdf = $attachment_dir . "/". $attachment_name . ".pdf";

	#$file_handle = fopen($attachment_location, "w+") or die("Error: Unable to open or create file");
	#move_uploaded_file($_FILES["inputentryname"]["tmp_name"], $target_file

	if($_FILES["cover"]["name"] != NULL and $_FILES["cover"]["type"] == 'application/pdf') {
		$cover_filename = pathinfo($_FILES["cover"]["name"], PATHINFO_FILENAME);
		move_uploaded_file($_FILES["cover"]["tmp_name"], str_replace(" ","-","$attachment_dir/{$_FILES["cover"]["name"]}"));
	}
	else {
		$error_message = "Missing Front Cover";
		require 'index.php';
		return;
	}

	

	if($_FILES["back"]["name"] != NULL and $_FILES["back"]["type"] == 'application/pdf') {
		$back_filename = pathinfo($_FILES["back"]["name"], PATHINFO_FILENAME);
		move_uploaded_file($_FILES["back"]["tmp_name"], str_replace(" ","-","$attachment_dir/{$_FILES["back"]["name"]}"));
	}
	else {
		$error_message = "Missing Back Cover";
		require 'index.php';
		return;
	}

	$blankcover = $_POST["blankcover"];

	$makeeven = $_POST["makeeven"];

	$TOC = $_POST["TOC"];

	$rightmargin = $_POST["right"];
	$leftmargin = $_POST["left"];
	$topmargin = $_POST["top"];
	$bottommargin = $_POST["bottom"];

	$file_text = '';
	$file_text .= FileHeader($cover_filename, $blankcover, $TOC, $rightmargin, $leftmargin, $topmargin, $bottommargin);
	

	foreach ($_FILES["files"]["error"] as $key => $error) {
    	if ($error == UPLOAD_ERR_OK and
    		$_FILES["files"]["type"][$key] == 'application/pdf') {

    		#ignore if either section name or file is NULL
    		if ($_POST["sectionnames"][$key] == NULL or 
    			$_FILES["files"]["name"][$key] == NULL) {
    			continue;
    		}

			$sectionnames_array[$key] = $_POST["sectionnames"][$key];
			$sectionfilenames_array[$key] = pathinfo($_FILES["files"]["name"][$key], PATHINFO_FILENAME);
			move_uploaded_file($_FILES["files"]["tmp_name"][$key], str_replace(" ","-","$attachment_dir/{$_FILES["files"]["name"][$key]}"));
			
			if (preg_match('/[$&%#_{}~^\\]/', $sectionnames_array[$key])) {
				$error_message = "Section Name: Disallowed Special Character in '$sectionnames_array[$key]'";
				require 'index.php';
				return;
			}

			$file_text .= NewSection($sectionnames_array[$key], $sectionfilenames_array[$key]);
		} else {
			$error_message = "failed upload/no file or not PDF";
			require 'index.php';
			return;
		}
	}

	$back_text = FileFooter($back_filename, NULL);

	$file_handle = fopen($attachment_location, "w+") or die("Error: Unable to open or create file");
	fwrite($file_handle, $file_text . $back_text);
	fclose($file_handle);


	chdir($attachment_dir);
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
	
	if (file_exists($attachment_location_pdf)) {
		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
		header("Cache-Control: public"); // needed for i.e.
		header("Content-type:application/pdf");
		header("Content-Disposition: attachment; filename=$attachment_name.pdf");
		readfile($attachment_location_pdf);
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

	$text .= "\\includepdf{{$attachment_dir}/$cover_filename.pdf}\n"; #coverpage
	
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

/*
\documentclass[twoside, letterpaper]{article}
\usepackage{pdfpages}
\usepackage{geometry}

\usepackage{fancyhdr}
\pagestyle{fancy}
\lhead{}\chead{}\rhead{}
\cfoot{}
\fancyfoot[LE,RO]{\thepage}

\renewcommand{\headrulewidth}{0pt}
\usepackage{tocloft}
\renewcommand\cftsecleader{\cftdotfill{\cftdotsep}}
\setlength\cftaftertoctitleskip{2cm}

\renewcommand{\contentsname}{\sffamily Contents} 

\begin{document}

\includepdf{"filename"}
\null\newpage % balnk backpage


\newgeometry{top=2in,bottom=1in,right=1.5in,left=1.5in} 
{\large\sffamily\tableofcontents}
\newpage
\newgeometry{top=1in,bottom=0.75in,right=0.25in,left=2in}
*/

#function NewSection
#
# $section_name: name of the section as it would appear in the TOC, ! no special characters
# $filename: file name, no extention, no path (pdf in same folder)
function NewSection($section_name, $filename)
{
	global $attachment_dir;
	$text = "\\addcontentsline{toc}{section}{{$section_name}}\n" .
			"\\includepdf[pages=-, pagecommand={}]{{$attachment_dir}/$filename.pdf}\n\n";
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

	$text .= "\\includepdf[pages=-, pagecommand={}]{{$attachment_dir}/$backcover_filename.pdf}\n" .
			"\\end{document}\n";

	return $text;
}

?>