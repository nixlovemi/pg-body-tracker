@extends('layout.site')

@section('SITE_BODY_CONTENT')
    @include('site.partials.other-pages-css')

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center position-relative">
                <div class="col-10 offset-1">
                    <div class="hero-content">
                    <h1 class="wow fadeInUp text-center" data-wow-delay=".4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        {{ __('messages.pages.siteTerms.title') }}
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
                                Leia com atenção as regras que definem como você pode utilizar nossa plataforma, quais são suas responsabilidades e os direitos oferecidos a você como usuário.
                            </p>
                            <small class="text-muted d-block mt-3">Atualizado em {{ now()->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Conteúdo centralizado --}}
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="policy-content">
                            <div class="row content-line">
                                <h5>1. Aceitação dos Termos</h5>
                                <p>
                                    Ao criar uma conta e utilizar o sistema {{ env('APP_NAME') }}, você concorda com estes Termos de Serviço e com nossa <a href="{{ route('site.privacy') }}">Política de Privacidade</a>. Caso não concorde, recomendamos que não utilize a plataforma.
                                </p>
                            </div>

                            <div class="row content-line">
                                <h5>2. Objetivo da Plataforma</h5>
                                <p>
                                    Esta plataforma oferece ferramentas para profissionais da área de saúde, fitness e bem-estar gerenciarem avaliações físicas de seus clientes, incluindo gráficos, metas, relatórios, reavaliações e gestão de progresso.
                                </p>
                            </div>

                            <div class="row content-line">
                                <h5>3. Cadastro e Responsabilidade</h5>
                                <p>
                                    O profissional é responsável pelas informações inseridas no sistema, incluindo dados dos seus clientes. Ao cadastrar-se, o usuário concorda em fornecer informações verídicas e manter seus dados atualizados.
                                </p>
                            </div>

                            <div class="row content-line">
                                <h5>4. Uso Adequado</h5>
                                <p>
                                    O uso da plataforma deve ser feito de forma ética e responsável. Observe que:
                                </p>
                                <ul>
                                    <li>Não é permitido utilizar o sistema para fins ilegais ou que infrinjam direitos de terceiros.</li>
                                    <li>O compartilhamento de dados sensíveis fora da plataforma é de total responsabilidade do profissional.</li>
                                    <li>O acesso à conta é individual e intransferível.</li>
                                </ul>
                            </div>

                            <div class="row content-line">
                                <h5>5. Planos e Pagamentos</h5>
                                <p>
                                    O {{ env('APP_NAME') }} oferece uma versão gratuita e planos pagos com recursos adicionais. Os detalhes dos planos e preços estão disponíveis na plataforma.
                                </p>
                                <ul>
                                    <li>A versão gratuita tem limitações de uso previamente definidas.</li>
                                    <li>Os planos pagos são cobrados de forma recorrente via plataforma de pagamento integrada (como o Mercado Pago).</li>
                                    <li>O cancelamento do plano premium pode ser feito a qualquer momento, mantendo-se o acesso até o fim do ciclo já pago.</li>
                                </ul>
                            </div>

                            <div class="row content-line">
                                <h5>6. Cancelamento e Exclusão de Conta</h5>
                                <p>
                                    O usuário pode solicitar o cancelamento da conta a qualquer momento. O sistema pode reter os dados pelo prazo necessário para cumprimento de obrigações legais, conforme descrito na Política de Privacidade.
                                </p>
                            </div>

                            <div class="row content-line">
                                <h5>7. Modificações na Plataforma</h5>
                                <p>
                                    O sistema poderá ser atualizado ou modificado para melhorias de desempenho, segurança e novas funcionalidades. O uso contínuo da plataforma implica aceitação das alterações.
                                </p>
                            </div>

                            <div class="row content-line">
                                <h5>8. Isenção de Responsabilidade</h5>
                                <p>
                                    Embora adotemos boas práticas de segurança e estabilidade, o {{ env('APP_NAME') }} não se responsabiliza por perdas de dados causadas por fatores externos, falhas na internet ou uso indevido por parte do usuário.
                                </p>
                            </div>

                            <div class="row content-line">
                                <h5>9. Propriedade Intelectual</h5>
                                <p>
                                    Todo o conteúdo da plataforma, incluindo design, marca, funcionalidades e código-fonte, são protegidos por direitos autorais e pertencem ao proprietário do sistema. É proibida a reprodução ou uso comercial sem autorização.
                                </p>
                            </div>

                            <div class="row content-line">
                                <h5>10. Foro e Legislação Aplicável</h5>
                                <p>
                                    Estes termos são regidos pelas leis brasileiras. Fica eleito o foro da comarca de Americana, Estado de São Paulo, como competente para dirimir quaisquer dúvidas ou controvérsias.
                                </p>
                            </div>

                            <div class="row content-line">
                                <h5>11. Contato</h5>
                                <p>
                                    Em caso de dúvidas, envie um e-mail para: <a href="mailto:{{ env('SUPPORT_EMAIL') }}">{{ env('SUPPORT_EMAIL') }}</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
