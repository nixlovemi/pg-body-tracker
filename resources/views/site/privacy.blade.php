@extends('layout.site')

@section('SITE_BODY_CONTENT')
    @include('site.partials.other-pages-css')

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center position-relative">
                <div class="col-10 offset-1">
                    <div class="hero-content">
                    <h1 class="wow fadeInUp text-center" data-wow-delay=".4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        {{ __('messages.pages.sitePrivacy.title') }}
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <section id="page-content" class="feature-section pt-50">
        <div class="feature-extended-wrapper">
            <div class="container">
                {{-- Título centralizado --}}
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-7 col-lg-9 col-md-10">
                        <div class="section-title text-center mb-60">
                            <p class="wow fadeInUp" data-wow-delay=".4s">
                                Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e protegemos os dados pessoais dos usuários que utilizam nossa plataforma de avaliação física.
                            </p>
                            <small class="text-muted d-block mt-3">Atualizado em {{ now()->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Conteúdo centralizado --}}
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="row content-line">
                            <h5>1. Controlador dos Dados</h5>
                            <p class="text-muted">
                                A empresa responsável pelo tratamento dos dados pessoais fornecidos na plataforma é {{ env('APP_NAME') }}, doravante referida como “nós” ou “nosso sistema”.
                            </p>
                        </div>

                        <div class="row content-line">
                            <h5>2. Dados Coletados</h5>
                            <p class="text-muted">
                                Coletamos os seguintes dados dos usuários e seus clientes:
                            </p>
                            <ul class="text-muted">
                                <li>Dados de identificação (nome, e-mail, telefone);</li>
                                <li>Dados de login e uso do sistema (IP, navegador, horário de acesso);</li>
                                <li>Dados corporais para fins de avaliação física (peso, altura, circunferências, percentual de gordura, fotos, entre outros);</li>
                                <li>Informações relacionadas a planos, metas e histórico de avaliações;</li>
                                <li>Dados de pagamento, exclusivamente para fins de processamento de assinaturas (através de terceiros como o Mercado Pago).</li>
                            </ul>
                        </div>

                        <div class="row content-line">
                            <h5>3. Finalidade do Tratamento</h5>
                            <p class="text-muted">
                                Os dados coletados são utilizados exclusivamente para:
                            </p>
                            <ul class="text-muted">
                                <li>Fornecer e manter o funcionamento adequado da plataforma;</li>
                                <li>Gerar relatórios de avaliação física e acompanhar a evolução dos clientes;</li>
                                <li>Melhorar a experiência do usuário;</li>
                                <li>Cumprir obrigações legais e regulatórias;</li>
                                <li>Processar pagamentos e gerenciar assinaturas.</li>
                            </ul>
                        </div>

                        <div class="row content-line">
                            <h5>4. Compartilhamento de Dados</h5>
                            <p class="text-muted">
                                Os dados dos clientes são acessíveis apenas pelo profissional que os cadastrou. Não compartilhamos dados pessoais com terceiros, exceto quando necessário para:
                            </p>
                            <ul class="text-muted">
                                <li>Processamento de pagamentos via plataformas integradas (ex: Mercado Pago);</li>
                                <li>Atendimento a exigências legais ou ordens judiciais.</li>
                            </ul>
                        </div>

                        <div class="row content-line">
                            <h5>5. Armazenamento e Segurança</h5>
                            <p class="text-muted">
                                Adotamos medidas técnicas e organizacionais apropriadas para proteger os dados pessoais contra acesso não autorizado, destruição, perda, alteração ou qualquer forma de tratamento inadequado ou ilícito.
                            </p>
                            <p class="text-muted">
                                Os dados são armazenados em servidores seguros, com acesso restrito e criptografia aplicada quando necessário.
                            </p>
                        </div>

                        <div class="row content-line">
                            <h5>6. Retenção dos Dados</h5>
                            <p class="text-muted">
                                Os dados serão armazenados enquanto houver vínculo com a plataforma. Após a exclusão da conta ou mediante solicitação expressa, os dados poderão ser excluídos definitivamente, respeitando obrigações legais de retenção mínima.
                            </p>
                        </div>

                        <div class="row content-line">
                            <h5>7. Direitos dos Titulares (LGPD)</h5>
                            <p class="text-muted">
                                Os titulares dos dados têm direito de:
                            </p>
                            <ul class="text-muted">
                                <li>Acessar e corrigir seus dados pessoais;</li>
                                <li>Solicitar a exclusão ou anonimização dos dados, quando cabível;</li>
                                <li>Revogar o consentimento a qualquer momento, quando aplicável;</li>
                                @php
                                /*<li>Portabilidade dos dados a outro serviço;</li>*/
                                @endphp
                                <li>Informações sobre o uso e compartilhamento dos dados.</li>
                            </ul>
                        </div>

                        <div class="row content-line">
                            <h5>8. Cookies</h5>
                            <p class="text-muted">
                                Utilizamos cookies essenciais para autenticação, navegação e funcionalidade da plataforma. O usuário pode configurar seu navegador para bloqueá-los, ciente de que isso poderá comprometer algumas funcionalidades.
                            </p>
                        </div>

                        <div class="row content-line">
                            <h5>9. Alterações nesta Política</h5>
                            <p class="text-muted">
                                Esta Política de Privacidade pode ser atualizada periodicamente. Recomendamos que o usuário revise esta página com frequência. Alterações significativas serão notificadas por e-mail ou dentro do sistema.
                            </p>
                        </div>

                        <div class="row content-line">
                            <h5>10. Contato</h5>
                            <p class="text-muted">
                                Em caso de dúvidas, solicitações ou para exercer seus direitos previstos na LGPD, entre em contato conosco pelo e-mail: <strong>suporte@pgbodytracker.com.br</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
