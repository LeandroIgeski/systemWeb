from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
import time

driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))

driver.maximize_window()

URL = "http://localhost/SistemaWeb/index.php"

# -----------------------
# Login válido
# -----------------------

driver.get(URL)

driver.find_element(By.NAME, "email").send_keys("admin@exemplo.com")
driver.find_element(By.NAME, "senha").send_keys("admin123")

driver.find_element(By.TAG_NAME, "button").click()

time.sleep(2)

if "admin.php" in driver.current_url or "dashboard.php" in driver.current_url:
    print("✓ Login válido: OK")
else:
    print("✗ Login válido: ERRO")

driver.get(URL)

# -----------------------
# Senha incorreta
# -----------------------

driver.find_element(By.NAME, "email").send_keys("admin@exemplo.com")
driver.find_element(By.NAME, "senha").send_keys("senhaerrada")

driver.find_element(By.TAG_NAME, "button").click()

time.sleep(2)

if "Senha inválida" in driver.page_source:
    print("✓ Senha incorreta: OK")
else:
    print("✗ Senha incorreta: ERRO")

driver.get(URL)

# -----------------------
# Email inexistente
# -----------------------

driver.find_element(By.NAME, "email").send_keys("naoexiste@email.com")
driver.find_element(By.NAME, "senha").send_keys("123456")

driver.find_element(By.TAG_NAME, "button").click()

time.sleep(2)

if "Email não encontrado" in driver.page_source:
    print("✓ Email inexistente: OK")
else:
    print("✗ Email inexistente: ERRO")

driver.get(URL)

# -----------------------
# Usuário desativado
# -----------------------

driver.find_element(By.NAME, "email").send_keys("usuario_desativado@email.com")
driver.find_element(By.NAME, "senha").send_keys("123456")

driver.find_element(By.TAG_NAME, "button").click()

time.sleep(2)

if "Usuário desativado" in driver.page_source:
    print("✓ Usuário desativado: OK")
else:
    print("✗ Usuário desativado: ERRO")

driver.quit()