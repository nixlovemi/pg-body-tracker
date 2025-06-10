@extends('layout.site')

@section('SITE_BODY_CONTENT')
    @include('site.partials.other-pages-css')

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center position-relative">
                <div class="col-10 offset-1">
                    <div class="hero-content">
                    <h1 class="wow fadeInUp text-center" data-wow-delay=".4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        {{ __('messages.pages.siteFaq.title') }}
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <section id="page-content" class="feature-section pt-60">
        <div class="feature-extended-wrapper">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-7 col-lg-9 col-md-10">
                        <div class="section-title text-center mb-60">
                            <p class="text-muted">
                                Aqui estão algumas das perguntas mais comuns sobre o uso do PG BodyTracker. Se ainda restar dúvidas, entre em contato com nosso suporte via suporte@pgbodytracker.com.br.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="accordion" id="faqAccordion">
                            <!-- Item 1 -->
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="faqHeading1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                        O que é o PG BodyTracker?
                                    </button>
                                </h5>
                                <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        É um sistema online criado para ajudar profissionais de saúde e fitness a realizarem avaliações físicas, acompanharem a evolução de seus clientes e gerarem relatórios profissionais com facilidade.
                                    </div>
                                </div>
                            </div>

                            <!-- Item 2 -->
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="faqHeading2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                        O sistema é gratuito?
                                    </button>
                                </h5>
                                <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Sim! Existe uma versão gratuita com recursos limitados. Para desbloquear funcionalidades avançadas, como exportação de relatórios, inclusão de fotos e envio de avaliações por link, você pode optar por um dos planos premium.
                                    </div>
                                </div>
                            </div>

                            <!-- Item 3 -->
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="faqHeading3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                        Como faço para assinar o plano premium?
                                    </button>
                                </h5>
                                <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Você pode acessar a página de planos e escolher entre as opções mensal, trimestral, semestral ou anual. Após confirmar o pagamento via Mercado Pago, seu plano é ativado automaticamente.
                                    </div>
                                </div>
                            </div>

                            <!-- Item 4 -->
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="faqHeading4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                        Posso cancelar a assinatura a qualquer momento?
                                    </button>
                                </h5>
                                <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Sim. Você pode cancelar a renovação automática a qualquer momento. O acesso ao plano premium permanece ativo até o fim do período já pago, sem reembolso parcial.
                                    </div>
                                </div>
                            </div>

                            <!-- Item 5 -->
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="faqHeading5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                        Os dados dos meus clientes são seguros?
                                    </button>
                                </h5>
                                <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Sim. Toda a plataforma segue boas práticas de segurança e os dados são armazenados com criptografia e acesso restrito. Apenas você, como profissional, tem acesso às informações dos seus clientes.
                                    </div>
                                </div>
                            </div>

                            <!-- Item 6 -->
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="faqHeading6">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6" aria-expanded="false" aria-controls="faqCollapse6">
                                        O sistema gera relatórios em PDF?
                                    </button>
                                </h5>
                                <div id="faqCollapse6" class="accordion-collapse collapse" aria-labelledby="faqHeading6" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Sim! Os relatórios das avaliações podem ser exportados em PDF e CSV, com opção de incluir seu logotipo e informações de contato. Esta função está disponível para usuários premium.
                                    </div>
                                </div>
                            </div>

                            <!-- Item 7 -->
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="faqHeading7">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse7" aria-expanded="false" aria-controls="faqCollapse7">
                                        O sistema funciona em celular?
                                    </button>
                                </h5>
                                <div id="faqCollapse7" class="accordion-collapse collapse" aria-labelledby="faqHeading7" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Sim, o PG BodyTracker foi projetado para funcionar em diferentes tamanhos de tela, incluindo smartphones e tablets.
                                    </div>
                                </div>
                            </div>

                            <!-- Item 8 -->
                            <div class="accordion-item mb-3">
                                <h5 class="accordion-header" id="faqHeading8">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse8" aria-expanded="false" aria-controls="faqCollapse8">
                                        O que acontece se eu atingir o limite de clientes?
                                    </button>
                                </h5>
                                <div id="faqCollapse8" class="accordion-collapse collapse" aria-labelledby="faqHeading8" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        O plano gratuito possui um limite de clientes cadastrados. Para adicionar mais, você pode fazer upgrade para o plano premium e ter acesso ilimitado.
                                    </div>
                                </div>
                            </div>

                            <!-- Add more if needed -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
