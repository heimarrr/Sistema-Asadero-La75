const { Builder, By, until } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');

async function testRegistrarProducto() {
    const options = new chrome.Options();
    options.addArguments('--start-maximized');
    // NO se agrega '--headless', así el navegador se ve en pantalla

    const driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    try {
        console.log('🚀 Iniciando E2E-07: Registrar producto');

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

            console.log('❌ E2E-07 FAIL');
            console.log(`   URL después del login: ${urlActual}`);
            console.log('   El login no llevó al usuario a /home');

            const textoPagina = await driver.findElement(
                By.tagName('body')
            ).getText();

            console.log('   Mensaje de la página:');
            console.log(textoPagina);

            return;
        }

        // 8. Ir a Productos
        await driver.get('http://localhost:5173/productos');

        // 9. Esperar que aparezca el botón Nuevo
        await driver.wait(
            until.elementLocated(By.css('button.pg-btn-new')),
            10000
        );

        console.log('   ✅ Página de productos cargada');

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

        console.log('   ✅ Formulario de producto abierto');

        // 12. Generar datos únicos
        const timestamp = Date.now();

        const nombre = `Producto E2E ${timestamp}`;
        const descripcion = 'Producto de prueba E2E';
        const stock = '50';
        const tipo = 'Insumo';
        const unidad = 'Unidad';
        const precioCompra = '1000';
        const precioVenta = '1500';

        // 13. Llenar campos de texto
        await driver.findElement(By.name('nombre')).sendKeys(nombre);
        await driver.findElement(By.name('descripcion')).sendKeys(descripcion);
        await driver.findElement(By.name('stock_actual')).sendKeys(stock);
        await driver.findElement(By.name('tipo')).sendKeys(tipo);
        await driver.findElement(By.name('unidad_medida')).sendKeys(unidad);
        await driver.findElement(By.name('precio_compra')).sendKeys(precioCompra);
        await driver.findElement(By.name('precio_venta')).sendKeys(precioVenta);

        // 14. Esperar a que el select de categorías cargue opciones (llegan async desde la API)
        const selectCategoria = await driver.findElement(By.name('id_categoria'));

        await driver.wait(async () => {
            const opciones = await selectCategoria.findElements(By.css('option'));
            return opciones.length > 1;
        }, 10000, 'Las categorías no cargaron a tiempo en el select');

        const opcionesCategoria = await selectCategoria.findElements(By.css('option'));
        const valorCategoria = await opcionesCategoria[1].getAttribute('value');
        await selectCategoria.sendKeys(await opcionesCategoria[1].getText());

        // 15. Dejar estado en Activo (valor por defecto)
        console.log('   ✅ Formulario completado');

        // 16. Buscar botón Guardar
        const botonGuardar = await driver.findElement(
            By.css('button.pg-btn-save')
        );

        // 17. Guardar producto
        await botonGuardar.click();

        console.log('   ⏳ Guardando producto...');

        // 18. Esperar respuesta
        await driver.sleep(2000);

        // 19. Obtener contenido actual de la página
        const pagina = await driver.getPageSource();

        // 20. Verificar que el producto aparezca
        if (pagina.includes(nombre)) {
            console.log('✅ E2E-07 PASS');
            console.log('   Producto registrado correctamente');
            console.log(`   Nombre: ${nombre}`);
            console.log(`   Categoría (id): ${valorCategoria}`);
        } else {
            console.log('❌ E2E-07 FAIL');
            console.log('   El producto no apareció en el listado');
        }

    } catch (error) {
        console.log('❌ E2E-07 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testRegistrarProducto();
