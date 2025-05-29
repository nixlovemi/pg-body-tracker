@extends('layout.core')

@section('CORE_BODY_CONTENT')
<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-4">PG Body Tracker</h1>
        <p class="lead">A ferramenta ideal para nutricionistas e educadores físicos avaliarem, acompanharem e impressionarem seus clientes.</p>
        <a href="javascript:;" class="btn btn-primary btn-lg mt-3">Crie sua conta grátis</a>
    </div>

    {{-- Funcionalidades principais --}}
    <div class="row text-center mb-5">
        <div class="col-md-4">
            <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
            <h4>Gráficos inteligentes</h4>
            <p>Acompanhe a evolução do cliente com gráficos precisos e comparativos.</p>
        </div>
        <div class="col-md-4">
            <i class="fas fa-file-pdf fa-3x mb-3 text-danger"></i>
            <h4>Relatórios profissionais</h4>
            <p>Gere PDFs com as informações detalhadas da avaliação, prontos para enviar.</p>
        </div>
        <div class="col-md-4">
            <i class="fas fa-users fa-3x mb-3 text-success"></i>
            <h4>Gestão de clientes</h4>
            <p>Organize seus clientes e avaliações em um painel simples e eficiente.</p>
        </div>
    </div>

    {{-- Comparativo Free vs Premium --}}
    <h2 class="text-center mb-4">Compare os planos</h2>
    <div class="table-responsive mb-5">
        <table class="table table-bordered text-center">
            <thead class="thead-light">
                <tr>
                    <th>Recurso</th>
                    <th>Free</th>
                    <th>Premium</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Número de clientes</td>
                    <td>Até 5</td>
                    <td>Ilimitado</td>
                </tr>
                <tr>
                    <td>Gráficos de evolução</td>
                    <td><i class="fas fa-check text-success"></i></td>
                    <td><i class="fas fa-check text-success"></i></td>
                </tr>
                <tr>
                    <td>Relatórios em PDF</td>
                    <td><i class="fas fa-times text-danger"></i></td>
                    <td><i class="fas fa-check text-success"></i></td>
                </tr>
                <tr>
                    <td>Envio por WhatsApp/Email</td>
                    <td><i class="fas fa-times text-danger"></i></td>
                    <td><i class="fas fa-check text-success"></i></td>
                </tr>
                <tr>
                    <td>Suporte prioritário</td>
                    <td><i class="fas fa-times text-danger"></i></td>
                    <td><i class="fas fa-check text-success"></i></td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Depoimento / CTA --}}
    <div class="text-center mb-5">
        <blockquote class="blockquote">
            <p class="mb-0">“Depois que comecei a usar o PG Body Tracker, meus clientes ficaram muito mais motivados.”</p>
            <footer class="blockquote-footer">André, Educador Físico</footer>
        </blockquote>
        <a href="javascript:;" class="btn btn-outline-primary btn-lg mt-3">Comece agora</a>
    </div>
</div>
@endsection
