<?php

namespace Tests\Browser;

use Faker\Factory;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PreencheFormularioTest extends DuskTestCase
{
    /**
     * Testa o preenchimento de um formulário.
     */
    public function testPreencheFormulario(): void
    {
        $this->browse(function (Browser $browser) {
            $faker = Factory::create();

            $browser
                ->visit('https://testpages.herokuapp.com/styled/basic-html-form-test.html')
                ->screenshot(time() . '_formulario_0.png')
                ->type('input[name="username"]', $faker->userName)
                ->type('input[name="password"]', $faker->password)
                ->type('textarea[name="comments"]', $faker->sentence)
                ->attach('input[name="filename"]', public_path('files/bionexo.png'))
                ->uncheck('input[name="checkboxes[]"][value="cb1"]')
                ->check('input[name="checkboxes[]"][value="cb2"]')
                ->check('input[name="checkboxes[]"][value="cb3"]')
                ->radio('input[name="radioval"]', $faker->randomElement(['rd1', 'rd2', 'rd3']))
                ->select('select[name="multipleselect[]"]', $faker->randomElements(['ms1', 'ms2', 'ms3', 'ms4'], 2))
                ->select('select[name="dropdown"]', $faker->randomElement(['dd1', 'dd2', 'dd3', 'dd4', 'dd5', 'dd6']))
                ->screenshot(time() . '_formulario_1.png')
                ->click('input[type="submit"]')
                ->screenshot(time() . '_formulario_2.png')
                ->assertSee('Processed Form Details');
        });
    }
}
