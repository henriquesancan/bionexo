<?php

namespace Tests\Browser;

use Illuminate\Support\Facades\File;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DownloadTest extends DuskTestCase
{
    /**
     * Testa o download de um arquivo.
     */
    public function testDownload(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('https://testpages.herokuapp.com/styled/download/download.html')
                ->screenshot(time() . '_download_0.png')
                ->click('a[id="direct-download-a"]')
                ->pause(10000);

            $path = public_path('files/downloads/Downloads');

            $files = collect(File::files($path))->sortByDesc(function ($file) {
                return $file->getMTime();
            });

            if ($files->isNotEmpty()) {
                $arquivo = $files->first();
                $arquivo = $arquivo->getPathname();

                $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

                File::move($arquivo, public_path('files/Teste TKS.' . $extensao));
            }
        });
    }
}
