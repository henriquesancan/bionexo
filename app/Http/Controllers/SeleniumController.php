<?php

namespace App\Http\Controllers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Faker\Factory;
use Illuminate\Support\Facades\File;

class SeleniumController extends Controller
{
    protected $driver;

    public function __construct()
    {
        $host = $_ENV['DUSK_DRIVER_URL'];

        $options = new ChromeOptions();
        $options->addArguments([
            '--disable-gpu',
            '--headless=new'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    /**
     * Testa o download de um arquivo.
     */
    public function testDownload()
    {
        $feedback = [
            'success' => false,
            'code' => 500,
            'message' => 'Tente novamente mais tarde.'
        ];

        try {
            $this->driver->get('https://testpages.herokuapp.com/styled/download/download.html');

            $this->driver->takeScreenshot(public_path('/prints/' . time() . '_download_0.png'));

            $this->driver->findElement(WebDriverBy::id('direct-download-a'))->click();

            sleep(30);

            $dir = public_path('/downloads/Downloads/');

            if (is_dir($dir)) {
                $files = collect(File::files($dir))->sortByDesc(function ($file) {
                    return $file->getMTime();
                });

                if ($files->isNotEmpty()) {
                    $arquivo = $files->first();
                    $arquivo = $arquivo->getPathname();

                    $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

                    File::move($arquivo, public_path('/downloads/Downloads/Teste TKS.' . $extensao));
                }
            }

            $feedback['success'] = true;
            $feedback['code'] = 200;
            $feedback['message'] = 'Download concluido com sucesso.';
        } catch (\Exception $exception) {
            $feedback['code'] = $exception->getCode();
            $feedback['message'] = $exception->getMessage();
        } finally {
            $this->driver->quit();

            return $feedback;
        }
    }

    /**
     * Testa o preenchimento de um formulário.
     */
    public function testPreencheFormulario()
    {
        $feedback = [
            'success' => false,
            'code' => 500,
            'message' => 'Tente novamente mais tarde.'
        ];

        try {
            $this->driver->get('https://testpages.herokuapp.com/styled/basic-html-form-test.html');

            $this->driver->takeScreenshot(public_path('/prints/' . time() . '_formulario_0.png'));

            $faker = Factory::create();

            $this->driver->findElement(WebDriverBy::cssSelector('input[name="username"]'))->sendKeys($faker->userName);
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="password"]'))->sendKeys($faker->password);
            $this->driver->findElement(WebDriverBy::cssSelector('textarea[name="comments"]'))->sendKeys($faker->sentence);
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="filename"]'))->sendKeys('/home/seluser/Files/bionexo.png');
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="checkboxes[]"][value="cb1"]'))->click();
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="checkboxes[]"][value="cb2"]'))->click();
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="checkboxes[]"][value="cb3"]'))->click();
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="radioval"][value="' . $faker->randomElement(['rd1', 'rd2', 'rd3']) . '"]'))->click();
            $this->driver->findElement(WebDriverBy::cssSelector('select[name="multipleselect[]"]'))->sendKeys($faker->randomElements(['ms1', 'ms2', 'ms3', 'ms4'], 2));
            $this->driver->findElement(WebDriverBy::cssSelector('select[name="dropdown"]'))->sendKeys($faker->randomElement(['dd1', 'dd2', 'dd3', 'dd4', 'dd5', 'dd6']));

            $this->driver->takeScreenshot(public_path('/prints/' . time() . '_formulario_1.png'));

            $this->driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();

            $this->driver->takeScreenshot(public_path('/prints/' . time() . '_formulario_2.png'));

            $htmlContent = $this->driver->getPageSource();

            $textToAssert = 'Processed Form Details';

            if (!strpos($htmlContent, $textToAssert)) throw new \Exception('Preenchimento não concluido.');

            $feedback['success'] = true;
            $feedback['code'] = 200;
            $feedback['message'] = 'Preenchimento concluido com sucesso.';
        } catch (\Exception $exception) {
            $feedback['code'] = $exception->getCode();
            $feedback['message'] = $exception->getMessage();
        } finally {
            $this->driver->quit();

            return $feedback;
        }
    }

    /**
     * Testa o upload de arquivo.
     */
    public function testUpload()
    {
        $feedback = [
            'success' => false,
            'code' => 500,
            'message' => 'Tente novamente mais tarde.'
        ];

        try {
            $this->driver->get('https://testpages.herokuapp.com/styled/file-upload-test.html');

            $this->driver->takeScreenshot(public_path('/prints/' . time() . '_upload_0.png'));

            $this->driver->findElement(WebDriverBy::cssSelector('input[name="filename"]'))->sendKeys('/home/seluser/Downloads/Teste TKS.txt');
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="filetype"]'))->click();
            $this->driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();

            $this->driver->takeScreenshot(public_path('/prints/' . time() . '_upload_1.png'));

            $htmlContent = $this->driver->getPageSource();

            $textToAssert = 'Uploaded File';

            if (!strpos($htmlContent, $textToAssert)) throw new \Exception('Upload não concluido.');

            $feedback['success'] = true;
            $feedback['code'] = 200;
            $feedback['message'] = 'Upload concluido com sucesso.';
        } catch (\Exception $exception) {
            $feedback['code'] = $exception->getCode();
            $feedback['message'] = $exception->getMessage();
        } finally {
            $this->driver->quit();

            return $feedback;
        }
    }
}
