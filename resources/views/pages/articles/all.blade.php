@extends('layout.template')
@section('content')
<main id="main" class="main">

    <div class="pagetitle d-flex">
        <div class="col-6">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Tableau de Bord</a></li>
                    <li class="breadcrumb-item active">Articles</li>
                </ol>
            </nav>
        </div>
        <div class="col-6 justify-content-end">
            <a href="/articles" class="btn btn-sm btn-primary">Retour</a>
        </div>
    </div><!-- End Page +++ -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-success d-none" id="tauxMsg">
                </div>
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
                        <h5 class="card-title">Tous les articles</h5>

                        <table id="example" class=" table table-bordered border-warning  table-hover table-warning table-sm">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Nom article</th>
                                    <th>Stock alerte</th>
                                    <th>Catégorie</th>
                                    <th>Unité de base</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($articles as $article)
                                <tr>
                                    <td>{{ $loop->index ++ }} </td>
                                    <td>{{ $article->nom }}</td>
                                    <td>{{ $article->stock_alert }}</td>
                                    <td>{{ $article->categorie->libelle }}</td>
                                    <td>{{ $article->uniteBase->unite }}</td>
                                    <td>
                                        {{-- @can('articles.modifier-article') --}}
                                        <a href="{{ route('articles.edit', $article->id) }}"
                                            class="btn btn-success"> <i class="bi bi-pencil"></i> </a>
                                        {{-- @endcan --}}
                                        {{-- @can('articles.enregistrer-stock') --}}
                                        <a class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop{{ $article->id }}"> <i
                                                class="bi bi-gear"></i> </a>
                                        {{-- @endcan --}}
                                        <a class="btn btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#stockModal{{ $article->id }}"> <i
                                                class="bi bi-box-seam"></i>
                                        </a>
                                    </td>
                                </tr>

                                @include('pages.articles.add-stock')

                                <!-- Modal -->
                                <div class="modal fade" id="staticBackdrop{{ $article->id }}"
                                    data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                    aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('tauxSupplements.store') }}" method="post">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Configurer
                                                        le taux</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @csrf
                                                    <div class="col-12">
                                                        <label for="" class="form-label">Unité de
                                                            base</label>
                                                        <input type="text" readonly
                                                            value="{{ $article->uniteBase->unite }}" required
                                                            class="form-control" name="unite">
                                                        <input type="hidden" value="{{ $article->id }}"
                                                            name="article_id">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Taux de conversion</label>
                                                        <input type="text" pattern="[0-9]+([,\.][0-9]+)?"
                                                            class="form-control" name="taux_conversion"
                                                            value="{{ old('taux_conversion') }}"
                                                            placeholder="Ex: 1 ou 2.6 (Nombre entier ou nombre à virgule)">
                                                        @if (old('taux_conversion') && !preg_match('/^[0-9]+(?:\.[0-9]+)?$/', old('taux_conversion')))
                                                        <div class="alert alert-danger">
                                                            Le champ doit contenir des chiffres ou un nombre à
                                                            virgule.
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="">Unité de mesure </label>
                                                        <select name="unite_mesure_id" id="unite_mesure_id"
                                                            class="form-control">
                                                            <option value="">Choisir l'unité à convertir
                                                            </option>
                                                            @foreach ($unites as $unite)
                                                            <option value="{{ $unite->id }}">
                                                                {{ $unite->unite }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Fermer</button>
                                                    <button type="submit"
                                                        class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @empty
                                <p class="text-center">Aucun résultat</p>
                                <!-- End Table with stripped rows -->
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $('#id_art_sel').select2({
        width: 'resolve'
    });
</script>
@endsection