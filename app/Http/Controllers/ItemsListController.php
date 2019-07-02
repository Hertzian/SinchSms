<?php

namespace App\Http\Controllers;

use App\Item;
use App\Account;
use App\ItemList;
use Clx\Xms\Client;
use App\MessageList;
use App\Imports\ItemImport;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Clx\Xms\Api\MtBatchTextSmsCreate;

class ItemsListController extends Controller
{
    protected $token;
    protected $splan;
    protected $endpoint;
    protected $sender;

    public function __construct(){

        $this->middleware('auth');        
        $this->token = config('app.sinch')['API_TOKEN'];
        $this->splan = config('app.sinch')['SERVICE_PLAN_ID'];
        // third arg for Client(), default https://api.clxcommunications.com/xms
        $this->endpoint = url('some url');
        $this->sender = '12345';
    }
    
    public function getBatches(){    
        $user = Auth::user();
        $account = Account::find($user->id);
        $batches = ItemList::where('account_id', $user->id)->get();

        $items = Item::select('name', 'number')
            ->where('item_list_id', '=' )
            ->get();
        // select('name', 'email as user_email')->get()

        // $name = Item::where('item_list_id', $batch->id)->pluck('name');

        // dd($items);
        // dd($id_batch);

        return view('itemlist.getitemslist',[
            'account' => $account,
            'batches' => $batches,
            // 'batch' => $id_batch,
            // 'itemlist' => $itemlist,
            'items' => $items
        ]);
    }

    public function newBatchView($id){
        $account = Account::find($id);

        return view('itemlist.newitemlist',[
            'account' => $account
        ]);
    }

    public function newBatch(Request $request, $id){
        $account = Account::find($id);
        $batch = new ItemList();

        $batch->name = $request->input('name');
        $batch->account_id = $account->id;

        $request->validate([
            'name' => 'required',
            // 'account_id' => 'required'
        ]);

        $batch->save();

        return redirect('/getlist')
        ->with('message', 'El batch se ha creado con éxito');
    }






    // public function csvToArray($filename = '', $delimiter = ','){
        
    //     if (!file_exists($filename) || !is_readable($filename))
    //     return false;

    //     $header = null;
    //     $data = array();
    //     if (($handle = fopen($filename, 'r')) !== false)
    //     {
    //         while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
    //         {
    //             if (!$header)
    //                 $header = $row;
    //             else
    //                 $data[] = array_combine($header, $row);
    //         }
    //         fclose($handle);
    //     }

    //     return $data;
    // }

    public function newCSVBatch(Request $request, $id){
        $account = Account::find($id);

        // $file = $request->file('csv')->getClientOriginalName();
        // $extension = $request->file('csv')->getClientOriginalExtension();

        $fileNameWithExt = $request->file('csv')->getClientOriginalName();

        // get just the filename
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

        // get extension
        $extension = $request->file('csv')->getClientOriginalExtension();

        // create new filename
        $fileNameToStore = $fileName . '_' . time() . '.' . $extension;

        // upload image
        $path = $request->file('csv')->storeAs('public/csv' . $request->input('csv'), $fileNameToStore);


        $this->validate($request, [
            'csv' => 'file'
        ]);

        Excel::import(new ItemImport, $path);

        
        // $file = public_path('file/test.csv');

        // $customerArr = $this->csvToArray($file);

        // for ($i = 0; $i < count($customerArr); $i ++)
        // {
        //     User::firstOrCreate($customerArr[$i]);
        // }

        return redirect('/getlist')->with('message', 'Los contactos se han adicionado con éxito');
    }





    // public function sendBatchSMS(Request $request, $id){        
    //     $account = Account::find($id);
    //     $batch = ItemList::find($account->id);
    //     // $batch = ItemList::where('account_id', $account->id)->get();
    //     // $name = Item::where('item_list_id', $batch->id)->pluck('name');
    //     $number = Item::where('item_list_id', $batch->id)
    //     // ->pluck('number')
    //     $names = [];
    //     $numbers = [];
    //     $message;
    //     dd($number);
    //     $client = new Client($this->splan, $this->token);        
    //     try {
    //         $batchParams = new MtBatchTextSmsCreate();
    //         $batchParams->setSender($this->sender);
    //         // if(count($number) >=1){
    //         //     for ($i=0; $i < count($name); $i++) { 
    //         //         $numbers[] = $number[$i];
    //         //     }
    //         // }
    //         $texto = $request->input('texto_personalizado');
    //         // $batchParams->setRecipients($numbers);
    //         $batchParams->setBody($texto);
    //         // $batchParams->setBody('Hola ${fulano}, ' . $texto);
    //         // if (count($name) >=1) {
    //         //     for ($i=0; $i < count($number); $i++) { 
    //         //         $names = array_add($names, $number[$i], $name[$i]);
    //         //     }
    //         //     $names += ['default' => 'estimado cliente'];
    //         // }
    //         // $fulano = ['fulano' => $names];
    //         // $batchParams->setParameters($fulano);
    //         $result = $client->createTextBatch($batchParams);
    //         $message = 'El ID que se dio al batch es: ' . $result->getBatchId();
    //     } catch (Exception $ex) {
    //         $message = 'Error creating batch: ' . $ex->getMessage();
    //     }
    //     return redirect('/')->with('message', $message);
    // }

    public function sendBatchSMS(Request $request, $id){

        $account = Account::find($id);
        $batchId = $request->input('item_list_id');
        $numbers = Item::where('item_list_id', $batchId)->pluck('number');

        $texto = $request->input('texto_personalizado');

        $request->validate([
            'texto_personalizado' => 'required',
        ]);

        $client = new Client($this->splan, $this->token);


        if (count($numbers) <= $account->message_limit && $account->message_limit >= 1) {
            try {

                $batchParams = new MtBatchTextSmsCreate();
                $batchParams->setSender($this->sender);
                $batchParams->setRecipients($numbers);
                $batchParams->setBody($texto);

                $result = $client->createTextBatch($batchParams);
                $batchID = $result->getBatchId();

                $batchSMS = new MessageList();
                $batchSMS->name = $batchID;
                $batchSMS->account_id = $account->id;
                $batchSMS->save();

                $account->message_limit = $account->message_limit - count($numbers);
                $account->balance = $account->balance - (count($numbers) * .65);
                $account->update();

                $message = 'El ID que se dio al batch es: ' . $batchID;
                
            } catch (Exception $ex) {

                $message = 'Error al crear el lote: ' . $ex->getMessage();
            }

        }else{
            $error = 'No cuentas con saldo disponible para enviar mensajes';

            return redirect('/')->with('error', $error);
        }

        return redirect('/')->with('message', $message);

    }

    public function deleteBatch($id){        
        $batch = ItemList::find($id);
        $batch->delete();

        return redirect('/getlist')
        ->with('message', 'El batch se ha eliminado con éxito');;
    }

    public function GetContact($id)
    {
        $user = Accont::find($id);
        
    }
    
}
