const { Builder, By, Until } = require('selenium-webdriver');
const { expect } = require('chai');

describe('Pruebas E2E - Compras y Abastecimiento de Stock', function () {
  this.timeout(60000); // 60 segundos de timeout para Selenium
  let driver;

  before(async function () {
    // Inicializar el navegador Chrome
    driver = await new Builder().forBrowser('chrome').build();
    await driver.manage().window().maximize();
  });

  after(async function () {
    // Cerrar el navegador al finalizar la suite
    if (driver) {
      await driver.quit();
    }
  });

  it('Debe registrar una nueva compra e incrementar el stock del producto', async function () {
    // 1. Navegar al módulo de compras
    await driver.get('http://localhost:3000/compras');

    // 2. Seleccionar el producto y completar el formulario
    // (Ajusta los IDs o XPaths según tu HTML real)
    const selectProducto = await driver.findElement(By.id('select-producto'));
    await selectProducto.sendKeys('Producto Demo');

    const inputCantidad = await driver.findElement(By.id('cantidad'));
    await inputCantidad.clear();
    await inputCantidad.sendKeys('10');

    const inputPrecio = await driver.findElement(By.id('precio-compra'));
    await inputPrecio.clear();
    await inputPrecio.sendKeys('50');

    // 3. Hacer clic en el botón guardar/registrar
    const btnGuardar = await driver.findElement(By.id('btn-guardar-compra'));
    await btnGuardar.click();

    // 4. Validar el mensaje de éxito o actualización en pantalla
    const mensajeExito = await driver.wait(
      until.elementLocated(By.className('alert-success')),
      5000
    );
    const textoMensaje = await mensajeExito.getText();

    expect(textoMensaje).to.include('Compra registrada con éxito');
  });
});