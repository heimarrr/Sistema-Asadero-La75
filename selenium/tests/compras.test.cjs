const { Builder, By, until } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');

async function testRegistrarCompra() {
    const options = new chrome.Options();
    options.addArguments('--start-maximized');

    const driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    try {
        console.log('🚀 Iniciando E2E-11: Registrar compra');

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

            console.log('❌ E2E-11 FAIL');
            console.log(`   URL después del login: ${urlActual}`);
            console.log('   El login no llevó al usuario a /home');

            const textoPagina = await driver.findElement(
                By.tagName('body')
            ).getText();

            console.log('   Mensaje de la página:');
            console.log(textoPagina);

            return;
        }

        // 8. Ir a Nueva Compra
        await driver.get('http://localhost:5173/compras/nueva');

        // 9. Esperar que cargue el select de proveedor
        const selectProveedor = await driver.wait(
            until.elementLocated(By.css('select.cp-select')),
            10000
        );

        console.log('   ✅ Página de nueva compra cargada');

        // 10. Esperar a que carguen los proveedores (async desde la API)
        await driver.wait(async () => {
            const opciones = await selectProveedor.findElements(By.css('option'));
            return opciones.length > 1;
        }, 10000, 'Los proveedores no cargaron a tiempo');

        // 11. Seleccionar el primer proveedor disponible
        const opcionesProveedor = await selectProveedor.findElements(By.css('option'));
        const nombreProveedor = await opcionesProveedor[1].getText();
        await selectProveedor.sendKeys(nombreProveedor);

        console.log(`   ✅ Proveedor seleccionado: ${nombreProveedor}`);

        // 12. Ubicar los selects/inputs de "Agregar Productos"
        // El primer select es proveedor, el segundo es producto
        const selects = await driver.findElements(By.css('select.cp-select'));
        const selectProducto = selects[1];

        // 13. Esperar a que carguen los productos
        await driver.wait(async () => {
            const opciones = await selectProducto.findElements(By.css('option'));
            return opciones.length > 1;
        }, 10000, 'Los productos no cargaron a tiempo');

        const opcionesProducto = await selectProducto.findElements(By.css('option'));
        const nombreProducto = await opcionesProducto[1].getText();
        await selectProducto.sendKeys(nombreProducto);

        console.log(`   ✅ Producto seleccionado: ${nombreProducto}`);

        // 14. Llenar cantidad y precio unitario
        const inputs = await driver.findElements(By.css('input.cp-input[type="number"]'));
        const inputCantidad = inputs[0];
        const inputPrecio = inputs[1];

        await inputCantidad.clear();
        await inputCantidad.sendKeys('10');

        await inputPrecio.clear();
        await inputPrecio.sendKeys('5000');

        // 15. Click en botón agregar producto (+)
        const botonAgregar = await driver.findElement(
            By.css('button.cp-btn-add')
        );

        await botonAgregar.click();

        console.log('   ✅ Producto agregado a la compra');

        // 16. Esperar que el producto aparezca en la tabla
        await driver.wait(
            until.elementLocated(By.css('table.cp-table tbody tr')),
            10000
        );

        // 17. Buscar botón de guardar/registrar compra (submit del formulario)
        const botonGuardar = await driver.findElement(
            By.css('form button[type="submit"]')
        );

        await botonGuardar.click();

        console.log('   ⏳ Registrando compra...');

        // 18. Esperar redirección a /compras
        await driver.wait(
            until.urlContains('/compras'),
            10000
        );

        await driver.sleep(1000);

        const urlFinal = await driver.getCurrentUrl();

        // 19. Verificar que la compra aparezca en el listado
        const pagina = await driver.getPageSource();

        if (
            urlFinal.includes('/compras') &&
            !urlFinal.includes('/nueva') &&
            pagina.includes(nombreProveedor)
        ) {
            console.log('✅ E2E-11 PASS');
            console.log('   Compra registrada correctamente');
            console.log(`   Proveedor: ${nombreProveedor}`);
            console.log(`   Producto: ${nombreProducto}`);
        } else {
            console.log('❌ E2E-11 FAIL');
            console.log('   La compra no se reflejó correctamente en el listado');
        }

    } catch (error) {
        console.log('❌ E2E-11 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testRegistrarCompra();
