<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\{Demande, Service};
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

/**
 * Class Utilisateur
 *
 * @property int $id
 * @property string|null $uuid
 * @property string|null $prenom
 * @property string|null $nom
 * @property string|null $email
 * @property string|null $telephone
 * @property string|null $password
 * @property int|null $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property Role|null $role
 * @property Collection|Demande[] $demandes
 * @property Collection|Service[] $services
 *
 * @package App\Models
 */
class Utilisateur extends Authenticatable implements MustVerifyEmail
{
	use SoftDeletes;
	protected $table = 'utilisateur';
	public static $snakeAttributes = false;
    use Notifiable;

    protected $casts = [
		'role_id' => 'int',
		'is_deleted' => 'bool',
		'is_root' => 'bool',
		'is_valide' => 'bool',
		'email_verified_at' => 'datetime'
	];

	protected $hidden = [
		'password',
		'token_update_password',
		'date_validated_token_password',
		'remember_token'
	];

	protected $fillable = [
		'uuid',
		'prenom',
		'nom',
		'email',
		'telephone',
		'password',
		'role_id',
		'is_deleted',
		'photo',
		'adresse',
		'is_root',
		'is_valide',
		'status_compte',
		'genre',
		'email_verified_at',
		'token_update_password',
		'date_validated_token_password',
		'remember_token'
	];

	public function role()
	{
		return $this->belongsTo(Role::class);
	}

    public function isCan($permission = null)
	{
		if(!$permission) return false;

		if ($this->role AND $this->role->nom == "admin") return true;

		return $this->role()->whereIn('id', function ($query) use ($permission) {
			$query->from('role_permission')
			->whereIn('permission_id', function ($query) use ($permission) {
				$query->from('permission')
				->whereNom($permission)
				->select('id')
				->get();
			})->select('role_id')->get();
		})->exists();
	}

    function isDemandeur() {
        return ($this->role AND $this->role->nom == "demandeur");
    }

    function isService() {
        return $this->services->count();
    }

    function getServiceDemandes() {

        return Demande::whereIn('type_demande_id', function ($query) {
            $query->from("type_demande")->whereIn('service_id', function ($query) {
                $query->from("service")->where('utilisateur_id', \auth()->user()->id)->select("id")->get();
            })->select('id')->get();
        });
    }

	public function demandes()
	{
		return $this->hasMany(Demande::class);
	}

	public function services()
	{
		return $this->hasMany(Service::class);
	}

    public function getServices()
	{
		return Service::whereIn('parent_id', function ($query) {
            $query->from("service")->whereUtilisateurId($this->id)->select('id')->get();
        });
	}

    function demandeRecus() {

        return Demande::whereIn('type_demande_id', function ($query) {
            $query->from("type_demande")->whereIn('service_id', function ($query) {
                $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
            })->select("id")->get();
        })->whereIn('demande.id', function ($query){
            $query->from("service_demande_service")->whereIn('service_destinataire_id', function ($query){
                $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
            })->select("demande_id")->get();
        });
    }

    function demandeTrannsmis() {

        return Demande::whereIn('type_demande_id', function ($query) {
            $query->from("type_demande")->whereIn('service_id', function ($query) {
                $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
            })->select("id")->get();
        })->whereIn('demande.id', function ($query){
            $query->from("service_demande_service")->whereIn('service_expediteur_id', function ($query){
                $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
            })->select("demande_id")->get();
        });
    }

    function getDemandes() {

        return Demande::whereIn('type_demande_id', function ($query) {
            $query->from("type_demande")->whereIn('service_id', function ($query) {
                $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
            })->select("id")->get();
        })->orWhere(function ($query) {
            $query->whereIn('demande.id', function ($query){
                $query->from("service_demande_service")->whereIn('service_expediteur_id', function ($query){
                    $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
                })->select("demande_id")->get();
            })->orWhereIn('demande.id', function ($query){
                $query->from("service_demande_service")->whereIn('service_destinataire_id', function ($query){
                    $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
                })->select("demande_id")->get();
            });
        });
    }

    function getDemandesByService() {

        return Demande::where(function ($query) {
            $query->whereIn('demande.id', function ($query){
                $query->from("service_demande_service")->whereIn('service_expediteur_id', function ($query){
                    $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
                })->select("demande_id")->get();
            })->orWhereIn('demande.id', function ($query){
                $query->from("service_demande_service")->whereIn('service_destinataire_id', function ($query){
                    $query->from("service")->where('utilisateur_id', $this->id)->select("id")->get();
                })->select("demande_id")->get();
            });
        });
    }

    function getName() {
        return "{$this->prenom} {$this->nom}";
    }

    function statistique($status = 0) {

        if(\auth()->user()->isService()) {
            return \auth()->user()->getDemandesByService()->whereStatus($status)->count();
        } elseif(\auth()->user()->isDemandeur()) {
            return $this->demandes()->whereStatus($status)->count();
        }

        return 0;
    }

}
