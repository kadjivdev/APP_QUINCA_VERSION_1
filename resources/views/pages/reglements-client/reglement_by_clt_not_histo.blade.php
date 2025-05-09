@extends('layout.template')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex">
        <div class="col-6">
            <h1 class="float-left">Règlements</h1>
        </div>
    </div><!-- End Page +++ -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        @if (!empty($reglements) && $reglements->isNotEmpty())
                        <h5 class="card-title">Liste des règlements de <strong>{{ $reglements[0]->client->nom_client }}</strong></h5>
                        @else
                        <h5 class="card-title">Aucun règlement trouvé pour ce client.</h5>
                        @endif


                        <table id="example"
                            class=" table table-bordered border-warning  table-hover table-warning table-sm">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>
                                        Code
                                    </th>
                                    <th>Date règlement</th>

                                    <th>Référence</th>
                                    <th>Montant règlement</th>
                                    <th>Type règlement</th>
                                    <th>Date Insertion</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1 ?>
                                @forelse ($reglements as $reglement)
                                <?php
                                $i++;
                                $montant = $reglement->montant_total_regle > 0 ? $reglement->montant_total_regle : $reglement->montant_regle;
                                ?>
                                <tr>
                                    <td>{{ $i }} </td>
                                    <td>{{ $reglement->code }}</td>
                                    <td>{{ $reglement->date_reglement->locale('fr_FR')->isoFormat('ll') }}</td>
                                    <td>{{ $reglement->reference }}</td>
                                    <td>{{ number_format($montant, 2, ',', ' ') }}</td>
                                    <td>{{ $reglement->type_reglement }}</td>
                                    <td>{{ $reglement->created_at }}</td>
                                    <td>
                                        @if(is_null($reglement->validated_at))
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-gear"></i>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                <li>
                                                    <a href="{{route('reglement-clt-validate' , $reglement->id)}}" onclick="return confirm('Êtes-vous sûr de vouloir valider le règlement ?')" data-bs-toggle="tooltip" class="dropdown-item" data-bs-placement="left" data-bs-title="Valider le règlement">Valider </a>
                                                </li>
                                                <li>
                                                    <a href="{{route('reglements-clt.edit', $reglement->id )}}" data-bs-toggle="tooltip" class="dropdown-item" data-bs-placement="left" data-bs-title="Modifier reglement">Modifier </a>
                                                </li>
                                                <li>
                                                    <a href="{{route('reglements-clt.show', $reglement->id )}}" data-bs-toggle="tooltip" class="dropdown-item" data-bs-placement="left" data-bs-title="Detail reglement">Détails </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('reglement-del-clt', $reglement->id) }}" method="POST" class="col-3">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item" data-bs-placement="left" data-bs-toggle="tooltip" onclick="return confirm('Êtes-vous sûr de vouloir supprimer le règlement ?')" data-bs-title="Supprimer le règlement">Supprimer</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>Aucun reglement enregistré</tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </section>
</main>
@endsection