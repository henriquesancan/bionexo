<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;

class PDFController extends Controller
{
    /**
     * Realiza a conversão de um PDF em texto e extrai informações específicas.
     *
     * @param Pdf $pdf
     * @param RegexController $regex
     *
     * @return array
     */
    public function converterPdf(): array
    {
        $pdf = new Pdf();
        $regex = new RegexController();

        // Caminho do arquivo PDF
        $caminho = public_path('files/demonstrativo.pdf');

        // Instancia o objeto PDFParser e define o arquivo PDF
        $pdfParser = $pdf->setPdf($caminho);

        // Extrai o texto do PDF
        $texto = $pdfParser->text();

        // Extração de informações do texto usando expressões regulares

        // Informações da Operadora
        $operadoraArea = $regex->origin($texto)->pre("OPERADORA")->pos("DADOS DO PRESTADOR")->get();
        $dados['registro_ans'] = intval($regex->origin($operadoraArea)->rule("(\d{6})")->get());
        $dados['operadora_nome'] = trim($regex->origin($operadoraArea)->pre("{$dados['registro_ans']}\n")->get());

        // Informações do Prestador
        $prestadorArea = $regex->origin($texto)->pre("DADOS DO PRESTADOR")->pos("DADOS DO LOTE/PROTOCOLO")->get();
        $dados['código_na_operadora'] = intval($regex->origin($prestadorArea)->rule("(\d{5})")->get());
        $dados['nome_contratado'] = trim($regex->origin($prestadorArea)->pre("7 - Nome do Contratado")->get());

        // Informações do Lote/Protocolo
        $loteProtocoloArea = $regex->origin($texto)->pre("DADOS DO LOTE/PROTOCOLO")->pos("TOTAL DO PROTOCOLO")->get();
        $dados['numero_lote'] = intval($regex->origin($loteProtocoloArea)->rule("(\d{1})")->get());
        $dados['numero_protocolo'] = intval($regex->origin($loteProtocoloArea)->rule("(\d{7})")->get());
        $dados['data_protocolo'] = trim($regex->origin($loteProtocoloArea)->pre("11- Data do Protocolo")->rule("(\d{2}\/\d{2}\/\d{4})")->get());
        $dados['codigo_glosa_protocolo'] = intval($regex->origin($loteProtocoloArea)->pre("12 - Código da Glosa do Protocolo")->get());

        // Informações do Total do Protocolo
        $totalProtocoloArea = $regex->origin($texto)->pre("TOTAL DO PROTOCOLO")->pos("TOTAL GERAL")->get();
        list(
            $dados['valor_informado_protocolo'],
            $dados['valor_processado_protocolo'],
            $dados['valor_liberado_protocolo'],
            $dados['valor_glosa_protocolo']
        ) = array_map([$this, 'converterNumero'], $regex->origin($totalProtocoloArea)->pre(null)->pos(null)->rule("(\d{1,2}\.\d{3},\d{2})")->all()->get());

        // Informações do Total Geral
        $totalGeralArea = $regex->origin($texto)->pre("TOTAL GERAL")->pos("OBSERVAÇÕES")->get();
        list(
            $dados['valor_informado_geral'],
            $dados['valor_processado_geral'],
            $dados['valor_liberado_geral'],
            $dados['valor_glosa_geral']
        ) = array_map([$this, 'converterNumero'], $regex->origin($totalGeralArea)->pre(null)->pos(null)->rule("(\d{1,2}\.\d{3},\d{2})")->all()->get());

        // Informações da Guias
        $guiasArea = $regex->origin($texto)->pre("DADOS DA GUIA")->pos("Impressão - Portal do Prestador")->all()->get();
        foreach ($guiasArea as $guiaItem => $guiaArea) {
            // Informações da Guia
            $dados['guias'][$guiaItem]['guia_no_prestador'] = intval($regex->origin($guiaArea)->pre("13 - Número da Guia no Prestador")->rule("(\d{10,13})")->get());
            $dados['guias'][$guiaItem]['senha'] = trim($regex->origin($guiaArea)->pre("15 - Senha")->get());
            $dados['guias'][$guiaItem]['nome_beneficiario'] = trim($regex->origin($guiaArea)->pre("16 - Nome do Beneficiário")->get());
            $dados['guias'][$guiaItem]['numero_carteira'] = trim($regex->origin($guiaArea)->pre("17 - Número da Carteira")->get());

            // Informações do Faturamento da Guia
            $faturamentoArea = $regex->origin($guiaArea)->pre("21 - Hora Fim do Faturamento")->pos("23 - Data de\nrealização")->get();
            list($data_inicio_faturamento, $data_fim_faturamento) = array_map('trim', $regex->origin($faturamentoArea)->rule("(\d{2}\/\d{2}\/\d{4})")->all()->get());
            list($hora_inicio_faturamento, $hora_fim_faturamento) = array_map('trim', $regex->origin($faturamentoArea)->rule("(\d{2}\:\d{2})")->all()->get());
            $dados['guias'][$guiaItem]['data_inicio_faturamento'] = $data_inicio_faturamento;
            $dados['guias'][$guiaItem]['hora_inicio_faturamento'] = $hora_inicio_faturamento;
            $dados['guias'][$guiaItem]['data_fim_faturamento'] = $data_fim_faturamento;
            $dados['guias'][$guiaItem]['codigo_glosa_guia'] = trim($regex->origin($faturamentoArea)->pre("{$hora_fim_faturamento}\n")->get());

            // Informações dos Procedimentos da Guia
            $procedimentosArea = $regex->origin($guiaArea)->pre("23 - Data de\nrealização\n")->pos("TOTAL DA GUIA")->get();
            $datas = array_map('trim', $regex->origin($procedimentosArea ?: $guiaArea)->pre(null)->pos(null)->rule("(\d{2}\/\d{2}\/\d{4})")->all()->get());
            $procedimentos = array_map('intval', $regex->origin($procedimentosArea ?: $guiaArea)->pre(null)->pos(null)->rule("(\d{8}) [a-zA-Z ().,\-]+")->all()->get());
            $valores = array_map([$this, 'converterNumero'], $regex->origin($procedimentosArea ?: $guiaArea)->pre(null)->pos(null)->rule("(\d{1,3},\d{2})")->all()->get());
            $glosa = array_map('trim', $regex->origin($procedimentosArea ?: $guiaArea)->pre(null)->pos(null)->rule("(\d{2}\.\d{2})")->all()->get());

            foreach ($procedimentos as $procedimentoItem => &$procedimentoTexto) {
                $descricao = trim($regex->origin($procedimentosArea ?: $guiaArea)->pre("{$procedimentoTexto} ", false)->rule("([\da-zA-Z ().,\-]+)")->get());
                if (Str::contains($dados['guias'][$guiaItem]['guia_no_prestador'], $procedimentoTexto)) {
                    unset($procedimentos[$procedimentoItem]);
                } else {
                    $procedimentoTexto .= " " . $descricao;
                }
            }

            unset($procedimentoItem);
            unset($procedimentoTexto);
            $procedimentos = array_values($procedimentos);
            $dados['guias'][$guiaItem]['valor_informado_guia'] = 0;
            $dados['guias'][$guiaItem]['valor_processado_guia'] = 0;
            $dados['guias'][$guiaItem]['valor_liberado_guia'] = 0;
            $dados['guias'][$guiaItem]['valor_glosa_guia'] = 0;

            foreach ($procedimentos as $procedimentoItem => $procedimentoTexto) {
                $dados['guias'][$guiaItem]['valor_informado_guia'] += $valores[$procedimentoItem];
                $dados['guias'][$guiaItem]['valor_processado_guia'] += $valores[$procedimentoItem + count($procedimentos)];
                $dados['guias'][$guiaItem]['valor_liberado_guia'] += $valores[$procedimentoItem + count($procedimentos) * 2];
                $dados['guias'][$guiaItem]['valor_glosa_guia'] += $this->converterNumero($glosa[$procedimentoItem], false);
                $dados['guias'][$guiaItem]['procedimentos'][$procedimentoItem] = [
                    'data' => $datas[$procedimentoItem],
                    'descricao' => $procedimentoTexto,
                    'valor_informado' => $valores[$procedimentoItem],
                    'valor_processado' => $valores[$procedimentoItem + count($procedimentos)],
                    'valor_liberado' => $valores[$procedimentoItem + count($procedimentos) * 2],
                    'valor_glosa' => $this->converterNumero($glosa[$procedimentoItem], false)
                ];
            }
        }

        return $dados;
    }

    /**
     * Converte um número no formato brasileiro (1.000,00) para o formato americano (1000.00).
     *
     * @param string $numero
     * @return float|null
     */
    protected function converterNumero(string $numero, bool $brasil = true): ?float
    {
        if ($brasil) $numero = Str::replace('.', '', $numero);

        $numero = Str::replace(',', '.', $numero);

        return is_numeric($numero) ? (float) $numero : null;
    }
}
