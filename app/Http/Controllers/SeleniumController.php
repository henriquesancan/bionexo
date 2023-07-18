<?php

namespace App\Http\Controllers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Faker\Factory;

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
     * Testa o preenchimento de um formulário.
     */
    public function testPreencheFormulario()
    {
        $feedback = [
            'success' => false,
            'message' => null
        ];

        try {
            $this->driver->get('https://testpages.herokuapp.com/styled/basic-html-form-test.html');

            $screenshotPath = public_path('files/capturas/' . time() . '_formulario_0.png');
            $this->driver->takeScreenshot($screenshotPath);

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

            $screenshotPath = public_path('files/capturas/' . time() . '_formulario_1.png');
            $this->driver->takeScreenshot($screenshotPath);

            $this->driver->findElement(WebDriverBy::cssSelector('input[type="submit"]'))->click();

            $screenshotPath = public_path('files/capturas/' . time() . '_formulario_2.png');
            $this->driver->takeScreenshot($screenshotPath);

            $htmlContent = $this->driver->getPageSource();

            $textToAssert = 'Processed Form Details';
            $isTextPresent = strpos($htmlContent, $textToAssert) !== false;

            $feedback['success'] = true;
            $feedback['message'] = $isTextPresent;
        } catch (\Exception $exception) {
            $feedback['message'] = $exception->getMessage();
        } finally {
            $this->driver->quit();

            return $feedback;
        }
    }
}
