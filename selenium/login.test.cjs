const { Builder, By, until } = require("selenium-webdriver");

(async function testLogin() {
    let driver = await new Builder().forBrowser("chrome").build();
    try {
        await driver.get("http://localhost:5173");
        await driver.wait(until.elementLocated(By.id("login")), 5000);
        await driver.findElement(By.id("login")).sendKeys("admin");
        await driver.findElement(By.id("password")).sendKeys("admin123");
        await driver.findElement(By.css('button[type="submit"]')).click();
        await driver.wait(until.urlContains("/home"), 8000);
        console.log("Login OK");
    } catch (err) {
        console.error("Falló login:", err.message);
        process.exitCode = 1;
    } finally {
        await driver.quit();
    }
})();
