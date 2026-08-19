const { Builder, By, until } = require('selenium-webdriver');

async function testAccesoDespuesLogout() {
    const driver = await new Builder()
        .forBrowser('chrome')
        .build();

    try {
        console.log('🚀 Iniciando E2E-05: Acceso después de cerrar sesión');

        // 1. Abrir login
        await driver.get('http://localhost:5173');

        // 2. Esperar campo de usuario
        const usuario = await driver.wait(
            until.elementLocated(By.name('login')),
            10000
        );

        // 3. Iniciar sesión
        await usuario.sendKeys('admin');

        const password = await driver.findElement(
            By.name('password')
        );

        await password.sendKeys('admin123');

        const botonLogin = await driver.findElement(
            By.css('button[type="submit"]')
        );

        await botonLogin.click();

        // 4. Esperar que entre al Home
        await driver.wait(
            until.urlContains('/home'),
            10000
        );

        console.log('   ✅ Login realizado correctamente');

        // 5. Buscar botón de cerrar sesión
        const botonLogout = await driver.wait(
            until.elementLocated(
                By.css('button.sb-out')
            ),
            10000
        );

        // 6. Cerrar sesión
        await botonLogout.click();

        // 7. Esperar que vuelva al login
        await driver.wait(
            until.urlIs('http://localhost:5173/'),
            10000
        );

        console.log('   ✅ Sesión cerrada');

        // 8. Intentar acceder directamente al Home
        await driver.get('http://localhost:5173/home');

        // 9. Esperar que PrivateRoute procese el acceso
        await driver.sleep(1500);

        // 10. Obtener URL actual
        const urlActual = await driver.getCurrentUrl();

        console.log(`   URL después del intento: ${urlActual}`);

        // 11. Verificar que no pueda acceder al Home
        if (!urlActual.includes('/home')) {
            console.log('✅ E2E-05 PASS');
            console.log('   El sistema bloqueó correctamente el acceso después del logout');
        } else {
            console.log('❌ E2E-05 FAIL');
            console.log('   El usuario pudo acceder al Home después del logout');
        }

    } catch (error) {
        console.log('❌ E2E-05 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testAccesoDespuesLogout();