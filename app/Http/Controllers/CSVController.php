<?php

namespace App\Http\Controllers;

use League\Csv\Writer;

class CSVController extends Controller
{
    /**
     * Gera um arquivo CSV com os dados extraídos do PDF.
     *
     * @return void
     */
    public function gerarCsv(): void
    {
        // Cria o objeto CSV a partir do caminho do arquivo e do modo de escrita
        $csv = Writer::createFromString();

        // Insere o cabeçalho no arquivo CSV
        $csv->insertOne([
            'registro_ans',
            'operadora_nome',
            'código_na_operadora',
            'nome_contratado',
            'numero_lote',
            'numero_protocolo',
            'data_protocolo',
            'codigo_glosa_protocolo',
            'valor_informado_protocolo',
            'valor_processado_protocolo',
            'valor_liberado_protocolo',
            'valor_glosa_protocolo',
            'valor_informado_geral',
            'valor_processado_geral',
            'valor_liberado_geral',
            'valor_glosa_geral',
            'guia_no_prestador',
            'senha',
            'nome_beneficiario',
            'numero_carteira',
            'data_inicio_faturamento',
            'hora_inicio_faturamento',
            'data_fim_faturamento',
            'codigo_glosa_guia',
            'valor_informado_guia',
            'valor_processado_guia',
            'valor_liberado_guia',
            'valor_glosa_guia',
            'data',
            'descricao',
            'valor_informado',
            'valor_processado',
            'valor_liberado',
            'valor_glosa',
        ]);

        // Instancia a classe PDFController para obter os dados
        $dados = new PDFController();
        $dados = $dados->converterPdf();

        // Itera sobre as guias e procedimentos para inserir as linhas no CSV
        foreach ($dados['guias'] as $guia) {
            foreach ($guia['procedimentos'] as $procedimento) {
                $csv->insertOne([
                    $dados['registro_ans'],
                    $dados['operadora_nome'],
                    $dados['código_na_operadora'],
                    $dados['nome_contratado'],
                    $dados['numero_lote'],
                    $dados['numero_protocolo'],
                    $dados['data_protocolo'],
                    $dados['codigo_glosa_protocolo'],
                    $dados['valor_informado_protocolo'],
                    $dados['valor_processado_protocolo'],
                    $dados['valor_liberado_protocolo'],
                    $dados['valor_glosa_protocolo'],
                    $dados['valor_informado_geral'],
                    $dados['valor_processado_geral'],
                    $dados['valor_liberado_geral'],
                    $dados['valor_glosa_geral'],
                    $guia['guia_no_prestador'],
                    $guia['senha'],
                    $guia['nome_beneficiario'],
                    $guia['numero_carteira'],
                    $guia['data_inicio_faturamento'],
                    $guia['hora_inicio_faturamento'],
                    $guia['data_fim_faturamento'],
                    $guia['codigo_glosa_guia'],
                    $guia['valor_informado_guia'],
                    $guia['valor_processado_guia'],
                    $guia['valor_liberado_guia'],
                    $guia['valor_glosa_guia'],
                    $procedimento['data'],
                    $procedimento['descricao'],
                    $procedimento['valor_informado'],
                    $procedimento['valor_processado'],
                    $procedimento['valor_liberado'],
                    $procedimento['valor_glosa'],
                ]);
            }
        }

        // Gera a saída do arquivo CSV
        $csv->output('planilha.csv');
    }
}
