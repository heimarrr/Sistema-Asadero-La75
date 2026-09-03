const { Builder, By, until } = require('selenium-webdriver');

async function testRegistrarUsuario() {
    const driver = await new Builder()
        .forBrowser('chrome')
        .build();

    try {
        console.log('🚀 Iniciando E2E-06: Registrar usuario');

        // 1. Abrir login
        await driver.get('http://localhost:5173/');

        // 2. Esperar campo de usuario
        const login = await driver.wait(
            until.elementLocated(By.name('login')),
            10000
        );

        // 3. Ingresar usuario administrador
        await login.sendKeys('admin');

        // 4. Ingresar contraseña
        const password = await driver.findElement(
            By.name('password')
        );

        await password.sendKeys('admin123');

        // 5. Buscar botón de iniciar sesión
        const botonLogin = await driver.findElement(
            By.css('button[type="submit"]')
        );

        // 6. Hacer clic en iniciar sesión
        await botonLogin.click();

        // 7. Esperar que el login redirija a /home
try {
    await driver.wait(
        until.urlContains('/home'),
        10000
    );

    console.log('   ✅ Administrador inició sesión');

} catch (error) {
    const urlActual = await driver.getCurrentUrl();

    console.log('❌ E2E-06 FAIL');
    console.log(`   URL después del login: ${urlActual}`);
    console.log('   El login no llevó al usuario a /home');

    // Mostrar texto de la página para identificar el error
    const textoPagina = await driver.findElement(
        By.tagName('body')
    ).getText();

    console.log('   Mensaje de la página:');
    console.log(textoPagina);

    return;
}

        console.log('   ✅ Administrador inició sesión');

        // 10. Ir a Usuarios
        await driver.get('http://localhost:5173/usuarios');

        // 11. Esperar que aparezca el botón Nuevo
        await driver.wait(
            until.elementLocated(By.css('button.pg-btn-new')),
            10000
        );

        console.log('   ✅ Página de usuarios cargada');

        // 12. Presionar Nuevo
        const botonNuevo = await driver.findElement(
            By.css('button.pg-btn-new')
        );

        await botonNuevo.click();

        // 13. Esperar formulario
        await driver.wait(
            until.elementLocated(By.name('nombre')),
            10000
        );

        console.log('   ✅ Formulario de usuario abierto');

        // 14. Generar datos únicos
        const timestamp = Date.now();

        const nombre = `Usuario E2E ${timestamp}`;
        const usuario = `e2e${timestamp}`;
        const correo = `e2e${timestamp}@test.com`;
        const contrasena = 'E2eTest123456';

        // 15. Llenar nombre
        await driver.findElement(
            By.name('nombre')
        ).sendKeys(nombre);

        // 16. Llenar usuario
        await driver.findElement(
            By.name('usuario')
        ).sendKeys(usuario);

        // 17. Llenar correo
        await driver.findElement(
            By.name('correo')
        ).sendKeys(correo);

        // 18. Llenar contraseña
        await driver.findElement(
            By.name('contrasena')
        ).sendKeys(contrasena);

        // 19. Seleccionar rol administrador
        const rol = await driver.findElement(
            By.name('id_rol')
        );

        await rol.sendKeys('1');

        // 20. Seleccionar estado activo
        const estado = await driver.findElement(
            By.name('estado')
        );

        await estado.sendKeys('1');

        console.log('   ✅ Formulario completado');

        // 21. Buscar botón Guardar
        const botonGuardar = await driver.findElement(
            By.css('button.pg-btn-save')
        );

        // 22. Guardar usuario
        await botonGuardar.click();

        console.log('   ⏳ Guardando usuario...');

        // 23. Esperar respuesta
        await driver.sleep(2000);

        // 24. Obtener contenido actual de la página
        const pagina = await driver.getPageSource();

        // 25. Verificar que el usuario aparezca
        if (
            pagina.includes(nombre) &&
            pagina.includes(usuario)
        ) {
            console.log('✅ E2E-06 PASS');
            console.log('   Usuario registrado correctamente');
            console.log(`   Nombre: ${nombre}`);
            console.log(`   Usuario: ${usuario}`);
            console.log(`   Correo: ${correo}`);
        } else {
            console.log('❌ E2E-06 FAIL');
            console.log('   El usuario no apareció en el listado');
        }

    } catch (error) {
        console.log('❌ E2E-06 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testRegistrarUsuario();