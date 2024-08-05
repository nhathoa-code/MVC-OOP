<?php

namespace NhatHoa\Framework\Facades;
use NhatHoa\Framework\Core\View;
use Dompdf\Dompdf;
use Dompdf\Options;

class PDF
{
    protected $template_path;
    protected $data;

    public function generate()
    {
        $view = new View($this->template_path);
        $view->setData($this->data);
        ob_start();
        $view->render();
        $html = ob_get_clean();

        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $dompdf = new Dompdf();
        $dompdf->setPaper(array(0, 0, 226.772, 841.889));

        $height = 0;

        $dompdf->setCallbacks([
            'myCallbacks' => [
                'event' => 'end_frame', 'f' => function ($frame) use(&$height){
                    $node = $frame->get_node();
                    if (strtolower($node->nodeName) === "body") {
                        $padding_box = $frame->get_padding_box();
                        $height += $padding_box['h'];
                    }
                }
            ]
        ]);

        $dompdf->loadHtml($html);
        $dompdf->render();
        unset($dompdf);
        $dompdf = new Dompdf($options);
        $dompdf->setPaper(array(0,0,226.772,$height + 30));
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream("invoice.pdf", array("Attachment" => 0));
    }

    public static function loadTemplate($template_path)
    {
        $pdf = new self();
        $pdf->template_path = $template_path;
        return $pdf;
    }

    public function setData(array $data = [])
    {
        $this->data = $data;
        return $this;
    }
}