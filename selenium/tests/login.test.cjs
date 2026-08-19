const { Builder, By, until } = require('selenium-webdriver');

async function testLoginExitoso() {
    const driver = await new Builder()
        .forBrowser('chrome')
        .build();

    try {
        console.log('🚀 Iniciando E2E-01...');

        // 1. Abrir la página de login
        await driver.get('http://localhost:5173');

        // 2. Esperar a que aparezca el campo de usuario
        const usuario = await driver.wait(
            until.elementLocated(By.name('login')),
            10000
        );

        // 3. Escribir usuario
        await usuario.sendKeys('admin');

        // 4. Buscar campo de contraseña
        const password = await driver.findElement(
            By.name('password')
        );

        // 5. Escribir contraseña
        await password.sendKeys('admin123');

        // 6. Buscar botón de inicio de sesión
        const botonLogin = await driver.findElement(
            By.css('button[type="submit"]')
        );

        // 7. Hacer clic
        await botonLogin.click();

        // 8. Esperar que cambie la URL
        await driver.wait(
            until.urlContains('/home'),
            10000
        );

        // 9. Obtener URL actual
        const urlActual = await driver.getCurrentUrl();

        // 10. Verificar resultado
        if (urlActual.includes('/home')) {
            console.log('✅ E2E-01 PASS');
            console.log('   Login realizado correctamente');
            console.log(`   URL: ${urlActual}`);
        } else {
            console.log('❌ E2E-01 FAIL');
            console.log('   No se llegó al dashboard');
        }

    } catch (error) {
        console.log('❌ E2E-01 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testLoginExitoso();