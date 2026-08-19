const { Builder, By, until } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');

async function testRegistrarProveedor() {
    const options = new chrome.Options();
    options.addArguments('--start-maximized');

    const driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    try {
        console.log('🚀 Iniciando E2E-10: Registrar proveedor');

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

            console.log('❌ E2E-10 FAIL');
            console.log(`   URL después del login: ${urlActual}`);
            console.log('   El login no llevó al usuario a /home');

            const textoPagina = await driver.findElement(
                By.tagName('body')
            ).getText();

            console.log('   Mensaje de la página:');
            console.log(textoPagina);

            return;
        }

        // 8. Ir a Proveedores
        await driver.get('http://localhost:5173/proveedores');

        // 9. Esperar que aparezca el botón Nuevo
        await driver.wait(
            until.elementLocated(By.css('button.pg-btn-new')),
            10000
        );

        console.log('   ✅ Página de proveedores cargada');

        // 10. Presionar Nuevo
        const botonNuevo = await driver.findElement(
            By.css('button.pg-btn-new')
        );

        await botonNuevo.click();

        // 11. Esperar formulario
        await driver.wait(
            until.elementLocated(By.name('nombre')),
            10000
        );

        console.log('   ✅ Formulario de proveedor abierto');

        // 12. Generar datos únicos
        const timestamp = Date.now();

        const nombre = `Proveedor E2E ${timestamp}`;
        const telefono = '3001234567';
        const correo = `proveedor${timestamp}@test.com`;
        const direccion = 'Calle Falsa 123';

        // 13. Llenar campos
        await driver.findElement(By.name('nombre')).sendKeys(nombre);
        await driver.findElement(By.name('telefono')).sendKeys(telefono);
        await driver.findElement(By.name('correo')).sendKeys(correo);
        await driver.findElement(By.name('direccion')).sendKeys(direccion);

        // 14. Dejar estado en Activo (valor por defecto)
        console.log('   ✅ Formulario completado');

        // 15. Buscar botón Guardar
        const botonGuardar = await driver.findElement(
            By.css('button.pg-btn-save')
        );

        // 16. Guardar proveedor
        await botonGuardar.click();

        console.log('   ⏳ Guardando proveedor...');

        // 17. Esperar respuesta
        await driver.sleep(2000);

        // 18. Obtener contenido actual de la página
        const pagina = await driver.getPageSource();

        // 19. Verificar que el proveedor aparezca
        if (pagina.includes(nombre)) {
            console.log('✅ E2E-10 PASS');
            console.log('   Proveedor registrado correctamente');
            console.log(`   Nombre: ${nombre}`);
            console.log(`   Correo: ${correo}`);
        } else {
            console.log('❌ E2E-10 FAIL');
            console.log('   El proveedor no apareció en el listado');
        }

    } catch (error) {
        console.log('❌ E2E-10 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testRegistrarProveedor();
