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
            Aquí encontrarás oportunidades laborales adaptadas a perfiles junior, así como recursos clave para mejorar tu empleabilidad:
            ofertas actualizadas, herramientas para optimizar tu CV y LinkedIn, consejos para destacar en entrevistas técnicas
            y mucho más.
        </p>
    </section>

    <div class="row align-items-center mb-5">
        <div class="col-md-6 mb-3 mb-md-0">
            <img src="assets/img/programming.jpg" alt="Programación y tecnología" class="img-fluid rounded shadow-sm">
        </div>
        
        <div class="col-md-6">
            <section class="bg-light p-4 rounded shadow-sm">
                <h3 class="fs-5 fw-bold">¿Quién está detrás de Orienta IT?</h3>
                <p class="mt-2 mb-0 justify-text">
                    Mi nombre es <strong>Álex Ricart López</strong> y estoy iniciando mi camino profesional como <strong>Desarrollador Web</strong>. <br> 
                    Dispongo de una trayectoria de más de 6 años en el ámbito de los Recursos Humanos y la orientación laboral. He trabajado con decenas de perfiles tecnológicos en su camino hacia su primer empleo,
                    lo que me ha permitido entender tanto las barreras que enfrentan los candidatos, como las necesidades reales de las empresas.
                </p>
                <p class="mt-2 mb-0 justify-text">
                    Gracias a esta experiencia, decidí crear una plataforma que conecte ambos mundos y aporte soluciones reales y eficaces.
                    <a href="https://alexrilodev.github.io/miportfolio/" target="_blank">¿Quiéres saber más sobre mí?</a>
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
