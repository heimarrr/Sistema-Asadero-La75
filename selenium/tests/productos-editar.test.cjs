const { Builder, By, until } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');

async function testEditarProducto() {
    const options = new chrome.Options();
    options.addArguments('--start-maximized');

    const driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();

    try {
        console.log('🚀 Iniciando E2E-08: Editar producto');

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

            console.log('❌ E2E-08 FAIL');
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

        // 9. Esperar que la tabla cargue al menos un producto
        await driver.wait(
            until.elementLocated(By.css('.pg-btn.edit')),
            10000
        );

        console.log('   ✅ Página de productos cargada');

        // 10. Tomar el botón de editar del primer producto de la lista
        const botonesEditar = await driver.findElements(
            By.css('.pg-btn.edit')
        );

        if (botonesEditar.length === 0) {
            console.log('❌ E2E-08 FAIL');
            console.log('   No hay productos en el listado para editar');
            return;
        }

        await botonesEditar[0].click();

        // 11. Esperar formulario con datos precargados
        const inputNombre = await driver.wait(
            until.elementLocated(By.name('nombre')),
            10000
        );

        // 12. Esperar a que el input traiga el valor del producto (no vacío)
        await driver.wait(async () => {
            const valor = await inputNombre.getAttribute('value');
            return valor && valor.length > 0;
        }, 10000, 'El formulario no cargó los datos del producto');

        const nombreOriginal = await inputNombre.getAttribute('value');

        console.log(`   ✅ Formulario de edición abierto (${nombreOriginal})`);

        // 13. Modificar la descripción y el precio de venta con datos únicos
        const timestamp = Date.now();
        const nuevaDescripcion = `Editado E2E ${timestamp}`;
        const nuevoPrecioVenta = '1999';

        const inputDescripcion = await driver.findElement(By.name('descripcion'));
        await inputDescripcion.clear();
        await inputDescripcion.sendKeys(nuevaDescripcion);

        const inputPrecioVenta = await driver.findElement(By.name('precio_venta'));
        await inputPrecioVenta.clear();
        await inputPrecioVenta.sendKeys(nuevoPrecioVenta);

        console.log('   ✅ Datos modificados');

        // 14. Buscar botón Guardar (en edición dice "Actualizar producto")
        const botonGuardar = await driver.findElement(
            By.css('button.pg-btn-save')
        );

        // 15. Guardar cambios
        await botonGuardar.click();

        console.log('   ⏳ Actualizando producto...');

        // 16. Esperar respuesta
        await driver.sleep(2000);

        // 17. Obtener contenido actual de la página
        const pagina = await driver.getPageSource();

        // 18. Verificar que los cambios se reflejen en el listado
        if (
            pagina.includes(nombreOriginal) &&
            pagina.includes(nuevaDescripcion) &&
            pagina.includes(nuevoPrecioVenta)
        ) {
            console.log('✅ E2E-08 PASS');
            console.log('   Producto actualizado correctamente');
            console.log(`   Producto: ${nombreOriginal}`);
            console.log(`   Nueva descripción: ${nuevaDescripcion}`);
            console.log(`   Nuevo precio de venta: ${nuevoPrecioVenta}`);
        } else {
            console.log('❌ E2E-08 FAIL');
            console.log('   Los cambios no se reflejaron en el listado');
        }

    } catch (error) {
        console.log('❌ E2E-08 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testEditarProducto();
