        <section id="home" class="hero-section">

            <div class="container">

                <div class="welcome-banner" role="status">
                    Welcome, <span id="welcomeName"><?= e(current_user()) ?></span>!
                </div>


                <div class="row align-items-center g-5">

                    <div class="col-lg-6">

                        <span class="hero-tag">
                            Your friendly neighborhood guide
                        </span>

                        <h1 class="hero-title mb-4">
                            Swing Into the <span>Web.</span>
                        </h1>

                        <p class="lead text-muted-2 mb-4">
                            Peter Parker is a teenager from Queens who gained spider-like powers and decided to use them to protect New York. This guide covers how he got his powers and what he can do with them.
                        </p>

                        <div class="d-flex flex-wrap gap-3">

                            <a href="#origin" class="btn btn-hero btn-lg px-4">
                                Read the Origin
                            </a>

                            <a href="#powers" class="btn btn-ghost btn-lg px-4">
                                See the Powers
                            </a>

                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="hero-card" role="img" aria-label="A spider swinging on a web thread above a comic-style caption">

                            <div class="floating-card card-one">Since 1962</div>
                            <div class="floating-card card-two">Queens, NYC</div>

                            <div class="swing-arm" aria-hidden="true">
                                <div class="swing-thread"></div>
                                <div class="swing-spider">🕷️</div>
                            </div>

                            <div class="hero-quote">
                                <h3>With great power comes great responsibility.</h3>
                                <p>The idea behind everything Spider-Man does.</p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>
