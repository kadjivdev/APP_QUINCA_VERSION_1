<?php

namespace App\Http\Controllers;

use App\Imports\ClientImport;
use App\Imports\ClientRanImport;
use App\Imports\ReportANouveauImport;
use App\Models\AcompteClient;
use App\Models\Agent;
use App\Models\Client;
use App\Models\CompteClient;
use App\Models\Departement;
use App\Models\Devis;
use App\Models\Facture;
use App\Models\FactureAncienne;
use App\Models\FactureVente;
use App\Models\ReglementClient;
use App\Models\LivraisonDirecte;
use App\Models\Requete;
use App\Models\Transport;
use App\Models\Vente;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $user = auth()->user();
        // $userPv = $user->boutique;
        // on recupere seulement les clients qui sont dans le departement du l'utilisateur connecté
        $clients = Client::with('departement')->with('agent')->whereNotIn('id', [880, 171, 537, 678])->get()
            ->filter(function ($client) use ($user) {
                if ($user->hasRole("Super Admin") || $user->hasRole("CHARGE DES STOCKS ET SUIVI DES ACHATS") || $user->hasRole("CAISSE") || $user->hasRole("RECOUVREMENT") || $user->hasRole("COMMERCIAL")) {
                    return $client;
                } else {
                    return $client->zone_id == $user->zone_id;
                }
            });

        foreach ($clients as $client) {
            $id = $client->id;
            // $devisIds = Devis::where('client_id', $id)->get()->pluck('id');
            // $facturesDevis = Facture::with(['typeFacture'])->whereIn("devis_id", $devisIds)->whereNotNull('validate_by')->get();
            // $facturesAnciennes = FactureAncienne::with(['typeFacture'])->where("client_id", $id)->get();
            // $factures_simples = LivraisonDirecte::with(['typeFacture'])->where("client_id", $id)->whereNotNull('validated_at')->get();
            // $factures = $facturesDevis->concat($factures_simples)->concat($facturesAnciennes);
            // $facturesSimples = $factures->filter(function ($facture) {
            //     if ($facture->typeFacture && $facture->typeFacture->libelle === 'Simple') {
            //         return true;
            //     }
            //     return false;
            // });

            // $total_du = $facturesSimples->sum('montant_total');
            // $total_solde = $facturesSimples->sum('montant_regle');
            // $total_restant = $total_du  - $total_solde;

            // $facturesNormalises = $factures->filter(function ($facture) {
            //     // Vérifier si typeFacture est défini et n'est pas nul
            //     if ($facture->typeFacture && $facture->typeFacture->libelle === 'Normalisée') {
            //         return true;
            //     }
            //     return false;
            // });

            // $montant_acompte = AcompteClient::where('client_id', $id)->whereNotNull('validator_id')->sum('montant_acompte');
            // $montant_requêtes = Requete::where('client_id', $id)->whereNotNull('validate_at')->sum('montant');
            // $montant_transports = Transport::where('client_id', $id)->whereNotNull('validate_at')->sum('montant');

            // $avance = $montant_acompte;
            // $total_du1 = $facturesNormalises->sum('montant_total');
            // $total_solde1 = $facturesNormalises->sum('montant_regle');
            // $total_restant1 = $total_du1  - $total_solde1;

            // $solde = $avance + $montant_requêtes - ($total_restant1 + $total_restant + $montant_transports);
            // $client->solde = $solde;

            $compte = CompteClient::where('client_id', $client->id)
                ->with(['facture' => function ($query) {
                    $query->select('id', 'devis_id'); // Sélectionnez les colonnes de la table facture
                }])
                ->orderBy('id', 'desc')
                ->get();

            $solde = 0;

            foreach ($compte as $transaction) {
                if ($transaction->type_op == "FAC" || $transaction->type_op == "FAC_AC" || $transaction->type_op == "FAC_VP" || $transaction->type_op == "FAC_VC" || $transaction->type_op == "FAC_RAN" || $transaction->type_op == "TRP") {
                    // Si c'est un règlement ou un acompte, on soustrait le montant
                    $solde -= $transaction->montant_op;
                } else {
                    // Pour tout autre type d'opération, on ajoute le montant
                    $solde += $transaction->montant_op;
                }

                if ($transaction->type_op == "FAC_VC") {
                    $facture = FactureVente::find($transaction->facture_id);
                    $transaction->vente_id = $facture->vente_id;
                }
            }

            $client->solde = $solde;
        }

        $departements = Departement::all();

        return view('pages.ventes-module.clients.index', compact('clients', 'departements'));
    }

    // les clients pour reglements
    function forForeglements()
    {
        $user = auth()->user();

        if (!$user->hasRole("RECOUVREMENT") && $user->hasRole("Super Admin") && $user->hasRole("CHARGE DES STOCKS ET SUIVI DES ACHATS")) {
            return "Vous n'êtes pas autorisé.e à faire cette action";
        }
        $clients = Client::with('departement')->with('agent')->whereNotIn('id', [880, 171, 537, 678])->get();

        foreach ($clients as $client) {
            $id = $client->id;
            $devisIds = Devis::where('client_id', $id)->get()->pluck('id');
            $facturesDevis = Facture::with(['typeFacture'])->whereIn("devis_id", $devisIds)->whereNotNull('validate_by')->get();
            $facturesAnciennes = FactureAncienne::with(['typeFacture'])->where("client_id", $id)->get();
            $factures_simples = LivraisonDirecte::with(['typeFacture'])->where("client_id", $id)->whereNotNull('validated_at')->get();
            $factures = $facturesDevis->concat($factures_simples)->concat($facturesAnciennes);
            $facturesSimples = $factures->filter(function ($facture) {
                if ($facture->typeFacture && $facture->typeFacture->libelle === 'Simple') {
                    return true;
                }
                return false;
            });

            $total_du = $facturesSimples->sum('montant_total');
            $total_solde = $facturesSimples->sum('montant_regle');
            $total_restant = $total_du  - $total_solde;

            $facturesNormalises = $factures->filter(function ($facture) {
                // Vérifier si typeFacture est défini et n'est pas nul
                if ($facture->typeFacture && $facture->typeFacture->libelle === 'Normalisée') {
                    return true;
                }
                return false;
            });

            $montant_acompte = AcompteClient::where('client_id', $id)->whereNotNull('validator_id')->sum('montant_acompte');
            $montant_requêtes = Requete::where('client_id', $id)->whereNotNull('validate_at')->sum('montant');
            $montant_transports = Transport::where('client_id', $id)->whereNotNull('validate_at')->sum('montant');

            $avance = $montant_acompte;
            $total_du1 = $facturesNormalises->sum('montant_total');
            $total_solde1 = $facturesNormalises->sum('montant_regle');
            $total_restant1 = $total_du1  - $total_solde1;

            $solde = $avance + $montant_requêtes - ($total_restant1 + $total_restant + $montant_transports);
            $client->solde = $solde;
        }

        $departements = Departement::all();

        return view('pages.ventes-module.clients.client-to-regle', compact('clients', 'departements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departements = Departement::orderBy('libelle', 'asc')->get();
        $agents = Agent::all();
        $zones = Zone::all();
        return view('pages.ventes-module.clients.create', compact('departements', 'agents', 'zones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_client' => 'required|string',
            // 'email' => 'email|unique:fournisseurs,email',
            // 'phone' => 'unique:users,phone',
            // 'address' => 'string',
            'seuil' => 'required|numeric',
            'departement' => 'required',
            'agent' => 'required',
            'zone_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (Client::latest()->first()) {
            $nbr = Client::latest()->first()->id;
        }

        $nbr = 0;
        $code = 'CL' . Str::random(4);
        if ($request->ajax()) {
            Client::create([
                'nom_client' => $request->nom_client,
                'code_client' => $code,
                'categorie' => 'VIP',
                'seuil' => $request->seuil,
                'departement_id' => $request->departement,
                'agent_id' => $request->agent,
                'zone_id' => $request->zone_id,
            ]);
            return response()->json(['success' => true,]);
        } else {

            Client::create([
                'nom_client' => $request->nom_client,
                'code_client' => $code,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'seuil' => $request->seuil,
                'categorie' => 'VIP',
                'departement_id' => $request->departement,
                'agent_id' => $request->agent,
                'zone_id' => $request->zone_id,
            ]);
            return redirect()->route('clients.index')
                ->with('success', 'Client ajouté avec succès.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $i = 1;
        $clients = Client::all();
        $client = Client::find($id);
        $devisIds = Devis::where('client_id', $id)->get()->pluck('id');
        // $venteIds = Vente::where('client_id', $id)->get()->pluck('id');
        $facturesDevis = Facture::with(['typeFacture'])->whereIn("devis_id", $devisIds)->get();
        $facturesComptant = FactureVente::join('facture_types', 'facture_types.id', '=', 'facture_ventes.facture_type_id')->where('client_facture', $id)
            ->get();

        $facturesAnciennes = FactureAncienne::with(['typeFacture'])->where("client_id", $id)->get();
        $factures_simples = LivraisonDirecte::with(['typeFacture'])->where("client_id", $id)->whereNotNull('validated_at')->get();
        $factures = $facturesDevis->concat($factures_simples)->concat($facturesAnciennes)->concat($facturesComptant)->sortByDesc('id');
        $facturesSimples = $factures->filter(function ($facture) {
            if ($facture->typeFacture && $facture->typeFacture->libelle === 'Simple') {
                return true;
            }
            return false;
        });

        // dd($facturesSimples);


        $total_du = $facturesSimples->sum('montant_total');
        $total_solde = $facturesSimples->sum('montant_regle');

        $total_restant = $total_du  - $total_solde;

        $facturesNormalises = $factures->filter(function ($facture) {
            // Vérifier si typeFacture est défini et n'est pas nul
            if ($facture->typeFacture && $facture->typeFacture->libelle === 'Normalisée') {
                return true;
            }
            return false;
        });

        $montant_acompte = AcompteClient::where('client_id', $id)->sum('montant_acompte');
        $montant_requêtes = Requete::where('client_id', $id)->whereNotNull('validate_at')->sum('montant');
        $montant_transports = Transport::where('client_id', $id)->whereNotNull('validate_at')->sum('montant');

        $avance = $montant_acompte + $montant_requêtes;
        $total_du1 = $facturesNormalises->sum('montant_total');
        $total_solde1 = $facturesNormalises->sum('montant_regle');
        $total_restant1 = $total_du1  - $total_solde1;

        $solde = $avance - ($total_restant1 + $total_restant + $montant_transports);

        $client->credit_total = $total_restant1 + $total_restant - $avance;
        $client->save();

        return view('pages.ventes-module.clients.show', compact(
            'i',
            'client',
            'clients',
            'factures',
            'total_du',
            'total_solde',
            'total_restant',
            'total_du1',
            'total_solde1',
            'total_restant1',
            'solde',
            'avance',
            'id',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Client::findOrFail($id);
        $departements = Departement::orderBy('libelle')->get();
        $agents = Agent::all();
        $zones = Zone::all();
        return view('pages.ventes-module.clients.edit', compact('client', 'departements', 'agents', 'zones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'nom_client' => 'required|string',
            'seuil' => 'required|numeric',
            'departement' => 'required',
            'agent' => 'required',
            'zone_id' => 'required',
        ]);

        $client = Client::find($id);
        $client->update([
            'nom_client' => $request->nom_client,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'seuil' => $request->seuil,
            'departement_id' => $request->departement,
            'agent_id' => $request->agent,
            'zone_id' => $request->zone_id,
        ]);

        return redirect()->route('clients.index')
            ->with('success', 'Client modifié avec succès.');
    }

    public function import_xls(Request $request)
    {
        $this->validate($request, [
            'upload'  => 'required'
        ]);

        Excel::import(new ClientImport, $request->file('upload'));

        return redirect()->route('clients.index')->with('status', 'Clients importés avec succès.');
    }

    public function import_ran_client_xls(Request $request)
    {
        $this->validate($request, [
            'upload'  => 'required'
        ]);

        Excel::import(new ClientRanImport, $request->file('upload'));

        return redirect()->route('clients.index')->with('status', 'Report a nouveau Clients importés avec succès.');
    }


    public function cltListAjax(Request $request)
    {
        $clients = Client::where('nom_client', 'LIKE', '%' . $request->input('term', '') . '%')
            ->where('seuil', '>', 'credit_total')
            ->whereNotIn('id', [880, 171, 537, 678])
            ->get();

        return response()->json([
            'clients'   => $clients,
        ]);
    }

    public function allClients(Request $request)
    {
        $clients = Client::where('nom_client', 'LIKE', '%' . $request->input('term', '') . '%')->whereNotIn('id', [880, 171, 537, 678])
            ->get();

        return response()->json([
            'clients'   => $clients,
        ]);
    }

    public function facturesParClt($id)
    {
        $client = Client::find($id);
        $devisIds = Devis::where('client_id', $id)->get()->pluck('id');
        $facturesDevis = Facture::with(['typeFacture'])->whereIn("devis_id", $devisIds)->get();
        $facturesAnciennes = FactureAncienne::where("client_id", $id)->get();
        $factures_simples = LivraisonDirecte::where("client_id", $id)->whereNotNull('validated_at')->get();
        $facturesTous = $facturesDevis->concat($factures_simples)->concat($facturesAnciennes);

        $factures = $facturesTous->filter(function ($facture) {
            return $facture->montant_total > $facture->montant_regle;
        });

        return response()->json([
            'factures'   => $factures->toArray(),
        ]);
    }

    public function devisByClient($id)
    {
        $devis = Devis::whereIn('id', function ($query) {
            $query->select('devis_id')
                ->from('factures');
        })
            ->where('client_id', $id)
            ->orderByDesc('id')
            ->get();

        return response()->json($devis);
    }

    public function reportNouveau(Request $request)
    {
        $this->validate($request, [
            'upload'  => 'required'
        ]);
        // dd($request->upload);

        Excel::import(new ReportANouveauImport, $request->file('upload'));

        return redirect()->route('clients.index')
            ->with('success', 'Report à nouveau effectué avec succès.');
    }

    public function synchronizeCompteClient()
    {
        // Parcours des lignes dans la table reglementClient avec validator_id non nul
        $reglements = ReglementClient::whereNotNull('validator_id')->get();

        foreach ($reglements as $reglement) {
            // Vérifie si une ligne correspondante existe dans compteClient
            $exists = CompteClient::where('client_id', $reglement->client_id)
                ->where('facture_id', $reglement->facture_id)
                ->where('montant_op', $reglement->montant_regle)
                ->exists();

            // dd($exists);

            if (!$exists) {
                // Crée une nouvelle ligne dans compteClient si elle n'existe pas
                CompteClient::create([
                    'date_op' => explode(' ', $reglement->date_reglement)[0],
                    'facture_id' => $reglement->facture_id,
                    'cle' => $reglement->facture_id,
                    'montant_op' => $reglement->montant_regle,
                    'user_id' => $reglement->user_id,
                    'client_id' => $reglement->client_id,
                    'type_op' => 'REG_VP',
                    'created_at' => $reglement->created_at,
                    'updated_at' => $reglement->created_at,
                ]);
            }
        }

        $acomptes = AcompteClient::whereNull('date_op')->whereNull('validator_id')->get();

        foreach ($acomptes as $acompte) {
            $acompte->validator_id = 1;
            $acompte->validated_at = now();

            try {
                $acompte->save(); // Sauvegarde les modifications
                echo "Acompte ID {$acompte->id} validé avec succès.\n";
            } catch (\Exception $e) {
                echo "Erreur lors de la validation de l'acompte ID {$acompte->id} : " . $e->getMessage() . "\n";
            }
        }

        return "Synchronisation terminée.";
    }
}
