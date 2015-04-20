<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BookBinder</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/starter-template.css" rel="stylesheet">

        <style type="text/css">

            div.top {
                background-image: url('css/Book3.svg');
                background-size: 100% 100%;
                background-repeat: no-repeat;
            }
            
        </style>
    </head>

    <body>
    
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <!--<span class="icon-bar"></span>-->
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href=".">BookBinder</a>
            </div>
            
            <div id="navbar" class="collapse navbar-collapse">
              <ul class="nav navbar-nav">
                <!--<li class="active"><a href="#">Home</a></li>-->
                <li><a href="#help" id="helpbtn" onclick="showHelp();">Help</a></li>
                <li><a href="about.php">About</a></li>
              </ul>
            </div>
          </div>
        </nav>

    <div class="top">
        <div class="container">
        
        <div class="starter-template">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= "<strong>ERROR!</strong> " . $error_message; ?></div>
            <?php endif; ?>
        </div>
        
        <div>
            <div class="control-group col-xs-6" id="fields">
                <div class="controls">
                    <form role="form" enctype="multipart/form-data" action="processform.php" method="post" autocomplete="off">
                        
                        <label>Front Cover</label>
                        <input class="form-control" name="cover" id="cover1" type="file">
                        <label><input type="checkbox" name="blankcover" checked> Add blank back to cover</label>
                        <br>
                        <br>
                        <label>Back cover</label>
                        <input class="form-control" name="back" type="file">
                        <label><input type="checkbox" name="makeeven" checked> Make back cover even if final page count is odd</label>
                        <br>
                        <br>
                        <label><input type="checkbox" name="TOC" checked> Generate table of contents</label>
                        <br>
                        <br>

                        <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            Page Margins
                        </button>
                        <div class="collapse" id="collapseExample">
                          <div class="well">
                            <label>Right Margin</label>
                            <input type="text" name="right" value="0.25" size="4">
                            <br>

                            <label>Left Margin</label>
                            <input type="text" name="left" value="2" size="4">
                            <br>

                            <label>Top Margin</label>
                            <input type="text" name="top" value="1" size="4">
                            <br>

                            <label>Bottom Margin</label>
                            <input type="text" name="bottom" value="0.75" size="4">
                            <br>
                          </div>
                        </div>

                        <br>
                        <br>

                        <label>Sections</label>
                        <span class="listspan">
                            <div class="entry input-group col-xs-12">
                                    <input class="form-control" name="sectionnames[]" type="text" placeholder="Section name" >
                                        <span class="input-group-addon"></span>
                                    <input class="form-control" name="files[]" type="file">
                                <span class="input-group-btn">
                                    <button class="btn btn-success btn-add" type="button">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </button>
                                </span>
                            </div>
                        </span>
                        <br>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

            <div id="helpdiv" style="display:none;">
                    <strong>Front Cover:</strong> PDF of book cover. (Required)<br>
                    <strong>Add blank back to cover:</strong> if checked, adds blank page after the cover, the cover sheet will only have the cover page.<br>
                    <strong>Back Cover:</strong> PDF of back of book. (Required)<br>
                    <strong>Make Back Cover Even:</strong> makes the PDF even paged such that when printed and bound the last page is the back cover, not blank.<br>
                    <strong>Generate Table of Contents:</strong> add a table of contents after the cover, with section names and page numbers.<br>
                    <strong>Page Margins:</strong> margins controling location of page number.<br>
                    <strong>Sections:</strong> a section name appearing in the table of contents and the PDF of the section.<br>
                    <strong>Use the + and - to add and remove sections from the selection.</strong>
            </div>
        </div>
    <div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/dynamicform.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#helpbtn").click(function(){
                    $("#helpdiv").toggle();
                });
            });
        </script>
    </body>
</html>
