const { Builder, By, until } = require('selenium-webdriver');

async function testLoginPasswordIncorrecta() {
    const driver = await new Builder()
        .forBrowser('chrome')
        .build();

    try {
        console.log('🚀 Iniciando E2E-02: Contraseña incorrecta');

        
        await driver.get('http://localhost:5173');

        
        const usuario = await driver.wait(
            until.elementLocated(By.name('login')),
            10000
        );

        
        await usuario.sendKeys('admin');

        
        const password = await driver.findElement(
            By.name('password')
        );

        
        await password.sendKeys('admin12345678');

        
        const botonLogin = await driver.findElement(
            By.css('button[type="submit"]')
        );

        
        await botonLogin.click();

        
        await driver.sleep(1500);

        
        const urlActual = await driver.getCurrentUrl();

        
        if (!urlActual.includes('/home')) {
            console.log('✅ E2E-02 PASS');
            console.log('   El sistema rechazó la contraseña incorrecta');
            console.log(`   URL actual: ${urlActual}`);
        } else {
            console.log('❌ E2E-02 FAIL');
            console.log('   El usuario ingresó al sistema');
        }

    } catch (error) {
        console.log('❌ E2E-02 FAIL');
        console.error(error);

    } finally {
        await driver.quit();
    }
}

testLoginPasswordIncorrecta();