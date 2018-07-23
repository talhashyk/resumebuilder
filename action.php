<?php

/**
 * Class to generate document and PDF from HTML
 * @param   string $input A string or array of where conditions.
 * @return  void
 * @since   1.0
 */

namespace API;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/lpkapil/phpdocx/classes/CreateDocx.php';

class Index {

    /**
     * PDF Path
     * @var type 
     */
    private $pdfPath;

    /**
     * Document Path
     * @var type 
     */
    private $docxPath;

    /**
     * File Name
     */
    private $fileName;

    /**
     * Site URL
     */
    private $siteUrl;

    /**
     * PDF Directory
     */
    private $pdfDir;

    /**
     * Docx Directory
     */
    private $docxDir;

    /**
     * Docx extension
     */
    private $docxExt;

    /**
     * PDF extension
     */
    private $pdfExt;

    /**
     * Template
     */
    private $template;

    /**
     * Class constructor
     */
    public function __construct($templatePath) {
        $this->pdfDir = 'pdf/';
        $this->docxDir = 'docx/';
        $this->pdfPath = __DIR__ . '/pdf/';
        $this->docxPath = __DIR__ . '/docx/';
        $this->fileName = 'Resume-' . time();
        $this->siteUrl = 'http://localhost/resumebuilder/';
        $this->docxExt = '.pdf';
        $this->pdfExt = '.docx';

        //Load template
        $this->template = $this->loadTemplate($templatePath);
        $this->_doCall($this->template);
    }

    /**
     * Generate PDF
     * @return void
     */
    protected function generatePdf($input) {
        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($input);
            $mpdf->Output($this->pdfPath . $this->fileName . '.pdf', 'F');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate Document
     * @return void
     */
    protected function generateDocx($input) {

        try {
            $docx = new \CreateDocx();
            $docx->embedHTML($input);
            $docx->createDocx($this->docxPath . $this->fileName);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generating Document and PDF
     */
    public function _doCall($template) {
        $this->generatePdf($template);
        $this->generateDocx($template);
        echo json_encode([
            'success' => true,
            'pdf' => $this->siteUrl . $this->pdfDir . $this->fileName . '.pdf',
            'docx' => $this->siteUrl . $this->docxDir . $this->fileName . '.docx',
                ], JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Load Template
     */
    private function loadTemplate($templatePath) {
        if (!file_exists($templatePath)) {
            return [
                'success' => false,
                'message' => 'Template does not exist'
            ];
        }

        $fileContent = file_get_contents($templatePath);

        if (!$fileContent) {
            return [
                'success' => false,
                'message' => 'Error in loading template, check permissions'
            ];
        }

        return $fileContent;
    }

    /**
     * Clean Generated
     *  @return void
     */
    public function cleanGenerated() {
        $pdfFiles = glob(__DIR__ . '/' . $this->pdfDir . '*' . $this->docxExt);
        $docxFiles = glob(__DIR__ . '/' . $this->docxDir . '*' . $this->pdfExt);
        $allFiles = array_merge($pdfFiles, $docxFiles);
        foreach ($allFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

}

new Index($_POST['template']);

//Calling
//$api = new Index();
//$input = <<< EOD
//        <!DOCTYPE HTML><html lang="en-US"><head><meta charset="UTF-8"><title>Resume</title><style>article,aside,details,figcaption,figure,footer,header,hgroup,nav,section{display:block}audio,canvas,video{display:inline-block;*display:inline;*zoom:1}audio:not([controls]){display:none}[hidden]{display:none}html{font-size:100%;overflow-y:scroll;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}body{margin:0;font-size:0.8em;line-height:1.4}body,button,input,select,textarea{font-family:sans-serif}a{color:#00e}abbr[title]{border-bottom:1px dotted}b,strong{font-weight:bold}blockquote{margin:1em 40px}dfn{font-style:italic}hr{display:block;height:1px;border:0;border-top:1px solid #ccc;margin:1em 0;padding:0}ins{background:#ff9;color:#000;text-decoration:none}mark{background:#ff0;color:#000;font-style:italic;font-weight:bold}pre,code,kbd,samp{font-family:monospace,serif;_font-family:'courier new',monospace;font-size:1em}pre{white-space:pre;white-space:pre-wrap;word-wrap:break-word}q{quotes:none}q:before,q:after{content:"";content:none}small{font-size:85%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sup{top:-0.5em}sub{bottom:-0.25em}ul,ol{margin:1em 0;padding:0 0 0 40px}dd{margin:0 0 0 40px}nav ul, nav ol{list-style:none;list-style-image:none;margin:0;padding:0}img{border:0;-ms-interpolation-mode:bicubic;vertical-align:middle}svg:not(:root){overflow:hidden}figure{margin:0}form{margin:0}fieldset{border:0;margin:0;padding:0}label{cursor:pointer}legend{border:0;*margin-left:-7px;padding:0}button,input,select,textarea{font-size:100%;margin:0;vertical-align:baseline;*vertical-align:middle}button,input{line-height:normal}button,input[type="button"],input[type="reset"],input[type="submit"]{cursor:pointer;-webkit-appearance:button;*overflow:visible}input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0}input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}textarea{overflow:auto;vertical-align:top;resize:vertical}input:valid,textarea:valid{}input:invalid,textarea:invalid{background-color:#f0dddd}table{border-collapse:collapse;border-spacing:0}td{vertical-align:top}.container{width:80%;max-width:600px;margin:0 auto}table{width:100%}thead{font-weight:bold}td{border:1px solid #ddd;padding:2px}.project>.title{margin-bottom:0px}.project>.meta{font-size:0.8em}.project>.meta>.date{float:right}.ir{display:block;border:0;text-indent:-999em;overflow:hidden;background-color:transparent;background-repeat:no-repeat;text-align:left;direction:ltr;*line-height:0}.ir br{display:none}.hidden{display:none !important;visibility:hidden}.visuallyhidden{border:0;clip:rect(0 0 0 0);height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px}.visuallyhidden.focusable:active,.visuallyhidden.focusable:focus{clip:auto;height:auto;margin:0;overflow:visible;position:static;width:auto}.invisible{visibility:hidden}.clearfix:before,.clearfix:after{content:"";display:table}.clearfix:after{clear:both}.clearfix{*zoom:1}</style></head><body><div class="container"> <header><h1 id="name">Your Name</h1><div id="designation"><span class="title">your designation</span> <span class="organization">The organisation name</span></div><div class="contact"><div class="email">john@example.com</div></div></header><div class="content" role=main><section id="objective"><h2 class="title">Objective</h2><div class="description"><p> Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p></div> </section><section id="Education"><h2 class="title">Education</h2><div class="description"><table><thead><td>Duration</td><td>Qualification</td><td>Institute/School</td><td>Result</td></thead><tr><td>abc</td><td>abc</td><td>abc</td><td>abc</td></tr><tr><td>abc</td><td>abc</td><td>abc</td><td>abc</td></tr></table></div> </section><section id="projects"><h2 class="title">Projects Undertaken</h2><div class="description"> <section class="project"><h3 class="title">Title of the project</h3><div class="meta"> <span class="field">Project Field</span> <span class="date">Aug 2010 - Nov2010</span></div><p class="description"> Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p> </section> <section class="project"><h3 class="title">Title of the project</h3><div class="meta"> <span class="field">Project Field</span> <span class="date">Aug 2010 - Nov2010</span></div><p class="description">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p> </section> <section class="project"><h3 class="title">Title of the project</h3><div class="meta"> <span class="field">Project Field</span> <span class="date">Aug 2010 - Nov2010</span></div><p class="description">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p> </section></div> </section></div></div></body></html>
//EOD;
//
//$path = $api->_doCall($input);
//echo "<pre>";
//print_r($path);
//echo "</pre>";
//Clean all generated via cron
//$api->cleanGenerated();

