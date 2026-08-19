const { Builder, By, until } = require('selenium-webdriver');

async function testAccesoSegunRol() {
    const driver = await new Builder()
        .forBrowser('chrome')
        .build();

    try {
        console.log('🚀 Iniciando E2E-03: Validar acceso según rol');

        // 1. Abrir login
        await driver.get('http://localhost:5173/');

        // 2. Esperar campo de usuario
        const usuario = await driver.wait(
            until.elementLocated(By.name('login')),
            10000
        );

        // 3. Ingresar usuario CAJERO (rol 2)
        await usuario.sendKeys('cajero');

        // 4. Buscar campo de contraseña
        const password = await driver.findElement(
            By.name('password')
        );

        // 5. Ingresar contraseña
        await password.sendKeys('12345678');

        // 6. Buscar botón de inicio de sesión
        const botonLogin = await driver.findElement(
            By.css('button[type="submit"]')
        );

        // 7. Iniciar sesión
        await botonLogin.click();

        // 8. Esperar que entre al sistema
        await driver.wait(
            until.urlContains('/home'),
            10000
        );

        console.log('   ✅ Usuario cajero inició sesión correctamente');

        // 9. Intentar acceder a una ruta exclusiva de administrador
        await driver.get('http://localhost:5173/usuarios');

        // 10. Esperar que RoleRoute procese el acceso
        await driver.sleep(1500);

        // 11. Obtener URL actual
        const urlActual = await driver.getCurrentUrl();

        console.log(`   URL después del intento: ${urlActual}`);

        // 12. Verificar que NO pueda acceder a /usuarios
        if (!urlActual.includes('/usuarios')) {
            console.log('✅ E2E-03 PASS');
            console.log('   El usuario con rol 2 no puede acceder a Usuarios');
        } else {
            console.log('❌ E2E-03 FAIL');
            console.log('   El usuario con rol 2 pudo acceder a Usuarios');
        }

    } catch (error) {
        console.log('❌ E2E-03 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testAccesoSegunRol();