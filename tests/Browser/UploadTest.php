<?php

namespace Tests\Browser;

use Faker\Factory;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UploadTest extends DuskTestCase
{
    /**
     * Testa o upload de um arquivo.
     */
    public function testUploadArquivo(): void
    {
        $this->browse(function (Browser $browser) {
            $faker = Factory::create();

            $browser
                ->visit('https://testpages.herokuapp.com/styled/file-upload-test.html')
                ->screenshot(time() . '_upload_0.png')
                ->attach('input[name="filename"]', public_path('files/Teste TKS.txt'))
                ->radio('input[name="filetype"]', $faker->randomElement(['image', 'text']))
                ->screenshot(time() . '_upload_1.png')
                ->click('input[type="submit"]')
                ->screenshot(time() . '_upload_2.png')
                ->assertSee('Uploaded File');
        });
    }
}
