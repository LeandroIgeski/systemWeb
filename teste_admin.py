from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
import time

driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))

driver.maximize_window()

# ------------------------
# Usuário não autenticado
# ------------------------

driver.get("http://localhost/SistemaWeb/admin.php")

time.sleep(2)

if "index.php" in driver.current_url:
    print("✓ Não autenticado bloqueado")
else:
    print("✗ Falhou")

# ------------------------
# Login usuário comum
# ------------------------

driver.get("http://localhost/SistemaWeb/index.php")

driver.find_element(By.NAME,"email").send_keys("user@exemplo.com")
driver.find_element(By.NAME,"senha").send_keys("password")

driver.find_element(By.TAG_NAME,"button").click()

time.sleep(2)

driver.get("http://localhost/SistemaWeb/admin.php")

time.sleep(2)

if "Acesso negado" in driver.page_source:
    print("✓ Usuário comum bloqueado")
else:
    print("✗ Falhou")

# ------------------------
# Login administrador
# ------------------------

driver.get("http://localhost/SistemaWeb/logout.php")

driver.get("http://localhost/SistemaWeb/index.php")

driver.find_element(By.NAME,"email").send_keys("admin@exemplo.com")
driver.find_element(By.NAME,"senha").send_keys("admin123")

driver.find_element(By.TAG_NAME,"button").click()

time.sleep(2)

driver.get("http://localhost/SistemaWeb/admin.php")

time.sleep(2)

if "Painel Administrativo" in driver.page_source:
    print("✓ Admin liberado")
else:
    print("✗ Falhou")

driver.quit()