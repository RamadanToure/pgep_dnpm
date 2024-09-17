<?php

use App\Models\Ong;
use App\Models\{RolePermission, Service, Demande};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Http;
use Propaganistas\LaravelPhone\PhoneNumber;

const SUCCESS = "#1abc9c";
const DANGER = "#d9534f";
const DARK = "#817d7c";

function authorize($ability = null) {
    if(!auth()->user()->isCan($ability)) abort(403, 'Unauthorized action.');
}

function local_money_format($montant, $symbole = ' GNF'){
	if (!$montant) return "0 $symbole";
    return number_format($montant, 0, ' ', ' ').$symbole;
}

function service_centrals() {
    return Service::whereIn('type_service_id', function ($query) {
        $query->from("type_service")->whereNom("Service central")->select("id")->get();
    })->get();
}


function map_permissions()
{
    return [
        "Utilisateur" => ['view_user','create_user', 'edit_user', 'delete_user'],
        'Rôle' => ['view_role','create_role', 'edit_role', 'delete_role'],
        'Permission' => ['view_permission','create_permission', 'edit_permission', 'delete_permission'],
        'Type' => ['view_type_ong','create_type_ong', 'edit_type_ong', 'delete_type_ong'],
        'ONG' => ['view_ong','create_ong', 'edit_ong', 'delete_ong'],
        'Type document' => ['view_type_document','create_type_document', 'edit_type_document', 'delete_type_document'],
        'Document' => ['view_document','create_document', 'edit_document', 'delete_document'],
    ];
}

function getRoleName($role){
    $descriptions = [
        'admin' => "Administrateur",
        'ong' => "Utilisateur demandeur d'agrement",
        'ministre' => "Ministre",
        'senasol' => "SENASOL",
        'consultant' => "Consultant"
    ];

    if (isset($descriptions[$role])) {
        return $descriptions[$role];
    } else {
        return 'Description non disponible';
    }
}

function getPermissionDescription($element) {
    $descriptions = [
        'view_user' => 'Afficher un utilisateur',
        'create_user' => 'Créer un utilisateur',
        'edit_user' => 'Modifier un utilisateur',
        'delete_user' => 'Supprimer un utilisateur',
        'view_role' => 'Afficher un rôle',
        'create_role' => 'Créer un rôle',
        'edit_role' => 'Modifier un rôle',
        'delete_role' => 'Supprimer un rôle',
        'view_permission' => 'Afficher une permission',
        'create_permission' => 'Créer une permission',
        'edit_permission' => 'Modifier une permission',
        'delete_permission' => 'Supprimer une permission',
        'view_type_ong' => 'Afficher un type de demande d\'agrement',
        'create_type_ong' => 'Créer un type de demande d\'agrement',
        'edit_type_ong' => 'Modifier un type de demande d\'agrement',
        'delete_type_ong' => 'Supprimer un type de demande d\'agrement',
        'view_ong' => 'Afficher une demande d\'agrement',
        'create_ong' => 'Créer une demande d\'agrement',
        'edit_ong' => 'Modifier une demande d\'agrement',
        'delete_ong' => 'Supprimer une demande d\'agrement',
        'view_type_document' => 'Afficher un type de document',
        'create_type_document' => 'Créer un type de document',
        'edit_type_document' => 'Modifier un type de document',
        'delete_type_document' => 'Supprimer un type de document',
        'view_document' => 'Afficher un document',
        'create_document' => 'Créer un document',
        'edit_document' => 'Modifier un document',
        'delete_document' => 'Supprimer un document',
    ];

    if (isset($descriptions[$element])) {
        return $descriptions[$element];
    } else {
        return 'Description non disponible';
    }
}


if(! function_exists('formatGNF')) {
    function formatGNF($value) {
        return number_format($value, 0, '.', ' ');
    }
}

function isAdmin(){
    return (Auth::user()->role AND (Auth::user()->role->nom == 'admin' OR Auth::user()->role->nom == 'ministre' OR Auth::user()->role->nom == 'senasol'));
}


function hasPermission($key){

    return Auth::user()->isCan($key);
    $isPermission=false;
    $permissions = RolePermission::where('role_id',auth()->user()->role_id)->with('permission')->get();
    foreach ($permissions as $value) {
        if($value->permission->nom==$key){
            $isPermission=true;
        }
    }
    return $isPermission;
}

function hasOng(){
    return Ong::where('utilisateur_id', auth()->user()->id)->first();
}
function dateFormat($date, $type = 'table'){
	if (empty($date)) {
		return "";
	}
 	switch ($type) {
 		case 'table':
 			return Carbon::parse($date)->locale('fr_FR')->isoFormat('LL');
 			break;
 		case 'mysql':
 			return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
 			break;
 		case 'mysql_time':
 			return Carbon::createFromFormat('d/m/Y H:i', $date)->format('Y-m-d H:i');
 			break;
 		case 'only_time':
 			return Carbon::createFromFormat('H:i', $date)->format(' H:i');
 			break;
 		case 'form':
 			return Carbon::parse($date)->format('d/m/Y');
 			break;
 		case 'isoFormat':
 			if (empty($date)) {
 				return trans("Jamais");
 			}
 			return Carbon::parse($date)->locale('fr_FR')->isoFormat('LLLL');
 			break;
 		case 'human':
 			return Carbon::parse($date)->diffForHumans();
 			break;
 		default:
 			return "";
 			break;
 	}
}

function send_sms($message, $telephone) {

    $key = "6cdf34aa4e95e37bbe8f9aa927fc1694";
    $secret = "fIATcSdxbzAmTKcxZgP8cdQ2nuUDPPhCT1v9Bfa3hJTEHFnkWxyh6hWg8BGtBudZ0V7sVTXsusYLiz-l-fVfJ1wyyBljkWrNDAjOMu6A5qE";
    $token = base64_encode("$key:$secret");

	try {

        $telephone = phone($telephone, "GN")->formatE164();

        $response = Http::withHeaders([
            'Authorization' => "Basic $token",
        ])->post('https://api.nimbasms.com/v1/messages', [
            'sender_name' => "SMS 9080",
            'message' => $message,
            'to' => [$telephone]
        ]);

        return $response->successful();

	} catch (\GuzzleHttp\Exception\ConnectException $e) {
		return false;
	} catch (\Twilio\Exceptions\RestException $e){
		return false;
	} catch (\Propaganistas\LaravelPhone\Exceptions\NumberParseException $e) {
		return false;
	} catch (\Illuminate\Http\Client\ConnectionException $e) {
        return false;
    }
}

function generate_code($n = 8) {
    $generator = "1357902468";

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
        $result .= substr($generator, (rand()%(strlen($generator))), 1);
    }
    return $result;
}

function generated_demande_number($n = 6){
	debut:
	$number = generate_code($n);
	if(Demande::whereNumeroDemande($number)->exists()) goto debut;
	return $number;
}
