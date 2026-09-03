const { Builder, By, until } = require('selenium-webdriver');

async function testLogout() {
    const driver = await new Builder()
        .forBrowser('chrome')
        .build();

    try {
        console.log('🚀 Iniciando E2E-04: Cerrar sesión');

        // 1. Abrir login
        await driver.get('http://localhost:5173/');

        // 2. Esperar campo de usuario
        const usuario = await driver.wait(
            until.elementLocated(By.name('login')),
            10000
        );

        // 3. Ingresar usuario válido
        await usuario.sendKeys('admin');

        // 4. Ingresar contraseña
        const password = await driver.findElement(
            By.name('password')
        );

        await password.sendKeys('admin123');

        // 5. Buscar botón de iniciar sesión
        const botonLogin = await driver.findElement(
            By.css('button[type="submit"]')
        );

        // 6. Iniciar sesión
        await botonLogin.click();

        // 7. Esperar que llegue al Home
        await driver.wait(
            until.urlContains('/home'),
            10000
        );

        console.log('   ✅ Login realizado correctamente');

        // 8. Buscar botón "Cerrar sesión"
        const botonLogout = await driver.wait(
            until.elementLocated(
                By.css('button.sb-out')
            ),
            10000
        );

        // 9. Hacer clic en cerrar sesión
        await botonLogout.click();

        // 10. Esperar que vuelva al login
        await driver.wait(
            until.urlIs('http://localhost:5173/'),
            10000
        );

        // 11. Obtener URL actual
        const urlActual = await driver.getCurrentUrl();

        // 12. Verificar resultado
        if (urlActual === 'http://localhost:5173/') {
            console.log('✅ E2E-04 PASS');
            console.log('   Sesión cerrada correctamente');
            console.log(`   URL actual: ${urlActual}`);
        } else {
            console.log('❌ E2E-04 FAIL');
            console.log(`   URL actual: ${urlActual}`);
        }

    } catch (error) {
        console.log('❌ E2E-04 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testLogout();