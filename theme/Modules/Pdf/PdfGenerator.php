<?php

namespace Theme\Modules\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator {
    private Dompdf $client;

    public function __construct(private readonly string $view, private array $data = [])
    {
        //Todo later make setOptions method or something
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $this->client = new Dompdf($options);

        $html = templates()->generate($this->view, $this->data);

        $this->client->loadHtml($html);
    }

    // Output the generated PDF to Browser
    public function stream(): void
    {
        //First render the PDF - saves it internally
        $this->client->render();

        //Then output to users browser
        $this->client->stream();
    }
}