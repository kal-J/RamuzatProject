<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once("vendor/dompdf/dompdf/src/Autoloader.php");
use Dompdf\Dompdf;
use Dompdf\Options;

class Pdfgenerator {
  public function generate($html, $filename, $stream, $paper, $orientation)
  {
$options = new Options();
$options->set('isRemoteEnabled', TRUE);

    $dompdf = new DOMPDF($options);
    $contxt = stream_context_create([ 
    'ssl' => [ 
            'verify_peer' => FALSE, 
            'verify_peer_name' => FALSE,
            'allow_self_signed'=> TRUE
        ] 
    ]);

    $dompdf->setHttpContext($contxt);
    $dompdf->loadHtml($html);
    $dompdf->setPaper($paper, $orientation);
    $dompdf->render();
    if ($stream==TRUE) {
        return $dompdf->stream($filename, array("Attachment" => 1));
    } else {
        return $dompdf->stream($filename, array('Attachment' => 0));
    }
  }
}
