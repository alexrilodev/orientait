<?php
require_once 'config.php';
?>
<?php include 'includes/header.php'; ?>

<main class="container py-5">
    <section class="text-center mb-5">
        <?php
        if (isset($_SESSION['usuario'])) {
            echo "<h2 class='fs-4 fw-semibold'>Bienvenido, <span class='text-purple'>" 
                 . htmlspecialchars($_SESSION['usuario']) . "</span></h2>";
        } else {
            echo "<h2 class='fs-4 fw-semibold'>¡Bienvenido a <span class='text-purple'>Orienta IT</span>!</h2>";
        }
        ?>
        <p class="fs-5 mt-3">
            Orienta IT es una plataforma digital pensada para ayudarte a impulsar tu carrera en el sector tecnológico.
            Aquí encontrarás oportunidades laborales así como recursos clave para mejorar tu empleabilidad.
        </p>
    </section>

    <div class="row align-items-center mb-5">
        <div class="col-md-6 mb-3 mb-md-0">
            <img src="assets/img/programming.jpg" alt="Programación y tecnología" class="img-fluid rounded shadow-sm">
        </div>
        
        <div class="col-md-6">
            <section class="bg-light p-4 rounded shadow-sm">
                <h3 class="fs-5 fw-bold">¿Por qué Orienta IT?</h3> <br>

                <p class="mt-2 mb-0 justify-text">
                    Mi nombre es <a href="https://alexrilodev.github.io/miportfolio/" target="_blank">Álex Ricart López</a> y soy <strong>Desarrollador Web</strong>.
                </p>
                <p class="mt-2 mb-0 justify-text">
                    He trabajado 4 años en Recursos Humanos, 2 años como Orientador Laboral y he ayudado a decenas de perfiles tecnológicos en su camino hacia su primer empleo.
                </p>
                <p class="mt-2 mb-0 justify-text">
                    Gracias a esta experiencia, me decidí en crear una plataforma que conectase ambos mundos y mejorase la empleabilidad.
                </p>
            </section>

            <section class="bg-light p-4 rounded shadow-sm">
                <h3 class="fs-5 fw-bold">Valores:</h3> <br>

                <p class="mt-2 mb-0 justify-text">
                    Orienta IT busca cubrir tus necesidades garantizando: 
                </p>
                <p class="mt-2 mb-0 justify-text">
                    · Respeto
                </p>
                <p class="mt-2 mb-0 justify-text">
                    · Profesionalidad
                </p>
                <p class="mt-2 mb-0 justify-text">
                    · Compromiso
                </p>
                <p class="mt-2 mb-0 justify-text">
                    · Empatía
                </p>
            </section>
        </div>
    </div>

    <section class="mt-5 text-center">
        <p class="fs-6">
            Tanto si estás dando tus primeros pasos en el mundo IT como si eres una empresa buscando talento, Orienta IT es el lugar ideal para crecer, conectar y avanzar.
        </p>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
