{{-- {{ <?php dd($factures);
exit(); ?>}} --}}

@extends('layout.template')

@section('content')
    <style>
        .card-title {
            padding: 6px 0 6px 0;
            font-size: 18px;
            font-weight: 500;
            color: #012970;
            font-family: "Poppins", sans-serif;
        }

        .card-body {
            padding: 0 20px 8px 20px;
        }
    </style>
    <main id="main" class="main">

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Changer de compte Fournisseur</h5>

                <form action="{{ url('/show_frs') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-11">
                            <select class="form-select mb-3" name="id_frs" id="id_frs">
                                <option value="">Choisir fournisseur </option>

                                @foreach ($fournisseurs as $fournisseurOne)
                                    <option value="{{ $fournisseurOne->id }}" data-donnee="{{ $fournisseurOne->name }}">
                                        {{ $fournisseurOne->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-1">
                            <button type="submit" class="btn btn-dark petit_bouton">Afficher</button>
                        </div>
                    </div>
            </div>
        </div>
        @php
        $isNegative = $solde < 0;
        @endphp

        <div class="pagetitle d-flex">
            <div class="col-12">
                <h1 class="float-left">Compte du fournisseurss {{ $fournisseur->name }}
                    <span style="color: {{ $isNegative ? 'red' : 'green' }};">
                       ::: Solde =  {{ number_format($solde, 2) }} FCFA
                    </span>
                </h1>
            </div>

        </div>

        <div class="row">
            <h5> Rapports des Factures Simples</h5>
            <div class="col-4">
                <div class="card border border-primary" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title ">{{ number_format($total_du, 2, ',', ' ') }} </h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Montant total</h6>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card border border-warning" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">{{ number_format($total_solde, 2, ',', ' ') }} </h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Montant total règlement</h6>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card border border-danger" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title text-danger">{{ number_format($total_restant, 2, ',', ' ') }} </h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Montant restant</h6>
                    </div>
                </div>
            </div>

        </div>


        <div class="row">
            <h5> Rapports des Factures Normalisées</h5>
            <div class="col-4">
                <div class="card border border-primary" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title ">{{ number_format($total_du1, 2, ',', ' ') }} </h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Montant total</h6>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card border border-warning" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">{{ number_format($total_solde1, 2, ',', ' ') }} </h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Montant total règlement</h6>
                    </div>
                </div>
            </div>

            <div class="col-4">
                <div class="card border border-danger" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title text-danger">{{ number_format($total_restant1, 2, ',', ' ') }} </h5>
                        <h6 class="card-subtitle mb-2 text-body-secondary">Montant restant</h6>
                    </div>
                </div>
            </div>

        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <h5 class="card-title mb-0">Liste des Factures</h5>
                                    <span class="badge rounded-pill bg-dark">{{ count($factures) }} Factures au
                                        total</span>
                                </div>

                            </div>

                            <table id="example"
                                class="table table-bordered border-warning  table-hover table-warning table-sm">

                                <thead>
                                    <tr>
                                        <th width="2%">N°</th>
                                        <th width="21%">
                                            Ref facture
                                        </th>
                                        <th width="15%">Date Ecriture</th>
                                        <th width="15%">Date Fact/Reg</th>
                                        <th width="15%">Montant facture</th>
                                        <th width="15%">Type Facture</th>
                                        <th width="15%">Régler / Restant</th>
                                        <th width="2%">...</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($factures as $facture)
                                        <tr>
                                            <td class="custom-background-facture">{{ $i++ }} </td>
                                            <td class="custom-background-facture">{{ $facture->ref_facture }} </td>
                                            <td class="custom-background-facture">
                                                {{ $facture->created_at->locale('fr_FR')->isoFormat('ll') }}</td>
                                            <td class="custom-background-facture">
                                                {{ $facture->date_facture->locale('fr_FR')->isoFormat('ll') }}</td>
                                            <td class="custom-background-facture">
                                                {{ number_format($facture->montant_total, 2, ',', ' ') }}</td>
                                            <td class="custom-background-facture">{{ $facture->typeFacture->libelle }}</td>
                                            <td class="custom-background-facture">
                                                <small style="color: green; text-decoration: underline;">Total
                                                    Régler</small>
                                                <br>
                                                {{ number_format($facture->montant_regle, 2, ',', ' ') }}
                                            </td>
                                            <td>
                                                @can('fournisseurs.details-d-une-facture-frs')
                                                    {{-- <a href="{{ route('details-factureFrs', $facture->id) }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-title="Voir détails" class="btn btn-dark petit_bouton"> <i
                                                            class="bi bi-eye"></i> </a> --}}
                                                    <a href="#" data-id="{{ $facture->id }}"
                                                        class="btn btn-dark petit_bouton details-button" data-bs-toggle="modal"
                                                        data-bs-target="#detailsModal">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                        @php
                                            $montantRestant = $facture->montant_total;
                                        @endphp
                                        @forelse($facture->reglements as $reglement)
                                            @php
                                                $montantRestant -= $reglement->montant_regle;
                                            @endphp
                                            <tr>
                                                <td></td>
                                                <td class="custom-background">{{ $reglement->code }} </td>
                                                <td class="custom-background">
                                                    {{ $reglement->created_at->locale('fr_FR')->isoFormat('ll') }}</td>
                                                <td class="custom-background">
                                                    {{ $reglement->date_reglement->locale('fr_FR')->isoFormat('ll') }}</td>
                                                <td class="custom-background">
                                                    {{ number_format($reglement->montant_regle, 2, ',', ' ') }}</td>
                                                <td class="custom-background">{{ $reglement->type_reglement }}</td>
                                                <td class="custom-background">
                                                    <small style="color: red; text-decoration: underline;">Restant</small>
                                                    <br>
                                                    {{ number_format($montantRestant, 2, ',', ' ') }}
                                                </td>

                                                <td>

                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td></td>
                                                <td class="custom-background"> <small>Aucune reglement pour cette
                                                        facture</small></td>
                                                <td class="custom-background"></td>
                                                <td class="custom-background">
                                                <td class="custom-background">

                                                </td>
                                                <td class="custom-background"></td>
                                                <td class="custom-background">
                                                    <small style="color: red; text-decoration: underline;">Restant</small>
                                                    <br>
                                                    {{ number_format($montantRestant, 2, ',', ' ') }}
                                                </td>
                                                <td></td>

                                            </tr>
                                        @endforelse
                                    @empty
                                        <tr>
                                            <td></td>
                                            <td class=""> <small text-danger text-center py-3>Aucune Facture pour ce
                                                    fournisseur</small></td>
                                            <td class=""></td>
                                            <td class="">
                                            </td>
                                            <td class=""></td>
                                            <td class=""></td>
                                            <td></td>
                                            <td></td>

                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Modal -->
        <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Détails de la facture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="modalContent">
                            <!-- Loader -->
                            <div id="loader" class="text-center" style="display: none;">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <p>Chargement des détails...</p>
                            </div>
                            <!-- Les détails de la facture seront insérés ici par AJAX -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    {{-- <script src="{{ asset('assets/js/jquery3.6.min.js') }}"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $('#id_frs').select2({
            width: 'resolve'
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.details-button').on('click', function() {
                var factureId = $(this).data('id'); // Obtenir l'ID de la facture

                // Afficher le loader
                $('#loader').show();
                $('#modalContent').empty(); // Vider le contenu précédent

                $.ajax({
                    url: '/details-facture/' + factureId, // L'URL de votre fonction
                    type: 'GET',
                    success: function(response) {
                        // Masquer le loader
                        $('#loader').hide();
                        // Remplir le contenu du modal avec la réponse
                        $('#modalContent').html(response);
                    },
                    error: function(xhr) {
                        // Masquer le loader
                        $('#loader').hide();
                        // Gérer les erreurs
                        $('#modalContent').html(
                            '<p>Erreur lors du chargement des détails de la facture.</p>');
                    }
                });
            });
        });
    </script>


@endsection
