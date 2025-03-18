@extends('layout.template')
@section('content')
    <main id="main" class="main">

        <div class="pagetitle d-flex">
            <div class="col-6">
                <h1 class="float-left">Rapport des Approvisionnements </h1>
            </div>

        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Liste des approvisionnements</h5>

                            <!-- Table with stripped rows -->
                            <table id="example" class="table table-bordered border-warning  table-hover table-warning table-sm">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Date</th>
                                        <th>Référence</th>
                                        <th>Réf Comande</th>
                                        <th>Fournisseur</th>
                                        {{-- <th>Cout de Revient</th> --}}
                                        <th>Chauffeur</th>
                                        <th>Véhicule</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0; ?>
                                    @forelse ($livraisons as $liv)
                                        <?php $i++; ?>

                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $liv->date_liv }}</td>
                                            <td>{{ $liv->ref_liv }}</td>
                                            <td>
                                                @if($liv->approvisionnements->isNotEmpty())
                                                    {{ $liv->approvisionnements[0]->commande->reference }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($liv->approvisionnements->isNotEmpty())
                                                    {{ $liv->approvisionnements[0]->commande->fournisseur->name }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            {{-- <td>{{ $liv->cout_revient }}</td> --}}
                                            <td>{{ $liv->nom_chauf }}</td>
                                            <td>{{ $liv->num_vehicule }}</td>
                                            <td>
                                                <a href="{{ url('/rapport_livraison_frs_detail', $liv->cle) }}"
                                                    class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-title="Voir détails"> <i class="bi bi-eye"></i> </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">Aucune commande avec livraison enregistrée</td>
                                        </tr>
                                    @endforelse


                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main>
@endsection
