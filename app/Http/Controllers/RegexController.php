<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

class RegexController extends Controller
{
    /**
     * Indica se deve obter todas as correspondências ou apenas a primeira.
     *
     * @var bool
     */
    protected $all = false;

    /**
     * Texto de origem para aplicar a expressão regular.
     *
     * @var string|null
     */
    protected $origin = null;

    /**
     * Parte posterior ao trecho que se deseja obter.
     *
     * @var string
     */
    protected $pos = "\n";

    /**
     * Parte anterior ao trecho que se deseja obter.
     *
     * @var string
     */
    protected $pre = "\n";

    /**
     * Regra da expressão regular.
     *
     * @var string
     */
    protected $rule = "(.*?)";

    /**
     * Define que todas as correspondências devem ser obtidas.
     *
     * @return $this
     */
    public function all()
    {
        $this->all = true;

        return $this;
    }

    /**
     * Define o texto de origem para aplicar a expressão regular.
     *
     * @param string $origin
     * @return $this
     */
    public function origin(string $origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Define a parte posterior ao trecho que se deseja obter.
     *
     * @param string $pos
     * @return $this
     */
    public function pos(?string $pos, bool $n = true)
    {
        if (!empty($pos)) {
            $pos = Str::replace(["/", "(", ")", "|", "-"], ["\/", "\(", "\)", "\|", "\-"], $pos);
            $pos = $n ? "{$pos}\n" : $pos;
        }

        $this->pos = $pos;

        return $this;
    }

    /**
     * Define a parte anterior ao trecho que se deseja obter.
     *
     * @param string $pre
     * @return $this
     */
    public function pre(?string $pre, bool $n = true)
    {
        if (!empty($pre)) {
            $pre = Str::replace(["/", "(", ")", "|", "-"], ["\/", "\(", "\)", "\|", "\-"], $pre);
            $pre = $n ? "{$pre}\n" : $pre;
        }

        $this->pre = $pre;

        return $this;
    }

    /**
     * Define a regra da expressão regular.
     *
     * @param string $rule
     * @return $this
     */
    public function rule(string $rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Executa a expressão regular e retorna as correspondências encontradas.
     *
     * @return array|string|null
     */
    public function get(string $start = "/", string $end = "/s"): array | null | string
    {
        $regex = implode(null, [$start, $this->pre, $this->rule, $this->pos, $end]);

        if ($this->all) {
            preg_match_all($regex, $this->origin, $matches);
        } else {
            preg_match($regex, $this->origin, $matches);
        }

        $this->resetProperties();

        return array_key_exists(1, $matches) ? $matches[1] : null;
    }

    /**
     * Reseta as propriedades da classe para seus valores padrão.
     *
     * @return void
     */
    protected function resetProperties()
    {
        $this->all = false;
        $this->origin = null;
        $this->pos = "\n";
        $this->pre = "\n";
        $this->rule = "(.*?)";
    }
}
