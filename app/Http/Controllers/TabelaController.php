<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class TabelaController extends Controller
{
    /**
     * Extrai e salva os dados da tabela.
     */
    public function extracao(Client $client, DOMDocument $dom, Registro $registro): array
    {
        $feedback = [
            'success' => false,
            'message' => 'Tente novamente mais tarde.'
        ];

        DB::beginTransaction();

        try {
            $url = 'https://testpages.herokuapp.com/styled/tag/table.html';

            $response = $client->request('GET', $url);

            $html = $response->getBody()->getContents();

            $dom->loadHTML($html);

            $tabela = $dom->getElementsByTagName('table')->item(0);

            $linhas = $tabela->getElementsByTagName('tr');

            foreach ($linhas as $linha) {
                $colunas = $linha->getElementsByTagName('td');

                if ($colunas->length === 2) {
                    $nome = $colunas->item(0)->nodeValue;
                    $valor = $colunas->item(1)->nodeValue;

                    $registro->create([
                        'nome' => $nome,
                        'valor' => $valor,
                    ]);
                }
            }

            $feedback['success'] = true;
            $feedback['message'] = null;

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            $feedback['message'] = $exception->getMessage();
        } finally {
            return $feedback;
        }
    }
}
