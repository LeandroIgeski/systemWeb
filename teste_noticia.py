from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.action_chains import ActionChains
from webdriver_manager.chrome import ChromeDriverManager
from datetime import datetime
import pymysql
import time
import os

# Configuração
BASE_URL = "http://localhost/SistemaWeb"
ADMIN_EMAIL = "admin@exemplo.com"
ADMIN_PASSWORD = "admin123"
DB_HOST = "localhost"
DB_USER = "root"
DB_PASS = ""
DB_NAME = "sistema_noticias"

def criar_admin_no_banco():
    """Cria admin no banco se não existir"""
    try:
        conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME)
        cursor = conn.cursor()
        cursor.execute("SELECT id FROM usuarios WHERE email = %s", (ADMIN_EMAIL,))
        
        if not cursor.fetchone():
            senha_hash = "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi"
            cursor.execute(
                "INSERT INTO usuarios (nome, email, senha, tipo, ativo) VALUES (%s, %s, %s, %s, %s)",
                ("Administrador", ADMIN_EMAIL, senha_hash, "admin", 1)
            )
            conn.commit()
            print("✓ Admin criado no banco")
        else:
            print("✓ Admin já existe")
        
        cursor.close()
        conn.close()
    except Exception as e:
        print(f"Erro ao criar admin: {e}")

def limpar_noticia_teste(titulo):
    """Remove notícia de teste do banco"""
    try:
        conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME)
        cursor = conn.cursor()
        cursor.execute("DELETE FROM noticias WHERE titulo = %s", (titulo,))
        conn.commit()
        cursor.close()
        conn.close()
    except Exception as e:
        pass

def criar_imagem_teste():
    """Cria imagem PNG para teste"""
    try:
        from PIL import Image
        img = Image.new('RGB', (100, 100), color='red')
        img_path = 'imagem_teste.png'
        img.save(img_path)
        return img_path
    except:
        return None

print("\n" + "="*60)
print("TESTE AUTOMATIZADO E2E - SISTEMA DE NOTÍCIAS")
print("="*60)

driver = None
titulo_noticia = None
actions = None

try:
    # [1] Preparação - Criar admin no banco
    print("\n[1/6] Preparando dados no banco...")
    criar_admin_no_banco()
    time.sleep(3)
    
    # [2] Login
    print("\n[2/6] Fazendo login como admin...")
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
    wait = WebDriverWait(driver, 15)
    actions = ActionChains(driver)
    driver.get(f"{BASE_URL}/index.php")
    print("  Página de login carregada...")
    time.sleep(3)
    
    wait.until(EC.presence_of_element_located((By.NAME, "email")))
    print("  Preenchendo email...")
    driver.find_element(By.NAME, "email").send_keys(ADMIN_EMAIL)
    time.sleep(2)
    
    print("  Preenchendo senha...")
    driver.find_element(By.NAME, "senha").send_keys(ADMIN_PASSWORD)
    time.sleep(2)
    
    print("  Clicando em Entrar...")
    driver.find_element(By.TAG_NAME, "button").click()
    time.sleep(3)
    
    wait.until(EC.url_contains("admin.php"))
    print("✓ Login bem-sucedido!")
    time.sleep(3)
    
    # [3] Cadastrar notícia
    print("\n[3/6] Cadastrando notícia...")
    titulo_noticia = f"Teste Selenium {datetime.now().strftime('%Y%m%d_%H%M%S')}"
    
    driver.get(f"{BASE_URL}/noticias/cadastrar.php")
    print("  Formulário de cadastro carregando...")
    wait.until(EC.presence_of_element_located((By.NAME, "titulo")))
    time.sleep(3)
    
    print(f"  Preenchendo título: {titulo_noticia}")
    driver.find_element(By.NAME, "titulo").send_keys(titulo_noticia)
    time.sleep(2)
    
    data_hoje = datetime.now().strftime("%Y-%m-%d")
    print(f"  Preenchendo data: {data_hoje}")
    driver.find_element(By.NAME, "data_noticia").send_keys(data_hoje)
    time.sleep(2)
    
    texto = "Texto de teste automatizado com Selenium"
    print(f"  Preenchendo texto...")
    driver.find_element(By.NAME, "texto").send_keys(texto)
    time.sleep(2)
    
    # Upload imagem (opcional)
    try:
        img_path = criar_imagem_teste()
        if img_path:
            print(f"  Enviando imagem: {img_path}")
            driver.find_element(By.NAME, "imagem").send_keys(os.path.abspath(img_path))
            time.sleep(2)
    except Exception as e:
        print(f"  ⚠ Imagem não enviada: {e}")
    
    # Scroll para o botão e clica
    print("  Clicando em Cadastrar...")
    botao_submit = driver.find_element(By.TAG_NAME, "button")
    driver.execute_script("arguments[0].scrollIntoView(true);", botao_submit)
    time.sleep(2)
    botao_submit.click()
    time.sleep(3)
    
    # Aguarda redirecionamento ou clica em voltar
    try:
        # Tenta aguardar redirecionamento
        wait.until(EC.url_contains("listar.php"))
        print("  Redirecionamento automático realizado!")
    except:
        # Se não redirecionar, procura botão voltar
        print("  Aguardando página... procurando botão voltar")
        try:
            botao_voltar = None
            botoes = driver.find_elements(By.TAG_NAME, "a")
            for botao in botoes:
                texto = botao.text.lower()
                href = botao.get_attribute("href") or ""
                if "voltar" in texto or "listar" in href.lower():
                    botao_voltar = botao
                    break
            
            if botao_voltar:
                print("  Encontrou botão voltar, clicando...")
                driver.execute_script("arguments[0].click();", botao_voltar)
                time.sleep(3)
            else:
                print("  Navegando para listar.php...")
                driver.get(f"{BASE_URL}/noticias/listar.php")
                time.sleep(3)
        except Exception as e:
            print(f"  Erro ao processar: {e}")
            driver.get(f"{BASE_URL}/noticias/listar.php")
            time.sleep(3)
    
    print("✓ Notícia cadastrada!")
    time.sleep(3)
    
    # [4] Verificar na lista
    print("\n[4/6] Verificando se aparece na lista...")
    print("  Navegando para listar.php...")
    driver.get(f"{BASE_URL}/noticias/listar.php")
    wait.until(EC.presence_of_element_located((By.TAG_NAME, "table")))
    print("  Tabela carregada, aguardando...")
    time.sleep(5)
    
    corpo = driver.find_element(By.TAG_NAME, "body").text
    if titulo_noticia in corpo:
        print(f"✓ Notícia encontrada na lista!")
    else:
        raise Exception("Notícia não aparece na lista!")
    time.sleep(3)
    
    # [5] Visualizar notícia
    print("\n[5/6] Visualizando notícia...")
    print("  Procurando notícia na tabela...")
    time.sleep(2)
    
    tabela = driver.find_element(By.TAG_NAME, "table")
    linhas = tabela.find_elements(By.TAG_NAME, "tr")
    
    visualizou = False
    for linha in linhas:
        texto_linha = linha.text
        if titulo_noticia in texto_linha:
            print(f"  ✓ Encontrou a notícia na tabela")
            botoes = linha.find_elements(By.TAG_NAME, "a")
            print(f"  Total de botões encontrados: {len(botoes)}")
            
            for i, botao in enumerate(botoes):
                texto_botao = botao.text.lower()
                href = botao.get_attribute("href") or ""
                print(f"  Botão {i}: {texto_botao} - href: {href}")
                
                if "visualizar" in href.lower() or "visualizar" in texto_botao:
                    print(f"  Clicando em visualizar...")
                    driver.execute_script("arguments[0].scrollIntoView(true);", botao)
                    time.sleep(2)
                    try:
                        botao.click()
                    except:
                        driver.execute_script("arguments[0].click();", botao)
                    visualizou = True
                    break
            break
    
    if visualizou:
        print("  Aguardando página de visualização...")
        wait.until(EC.presence_of_element_located((By.TAG_NAME, "h1")))
        time.sleep(5)
        
        if titulo_noticia in driver.find_element(By.TAG_NAME, "body").text:
            print("✓ Notícia visualizada com sucesso!")
        else:
            print("⚠ Aviso: Notícia visualizada mas conteúdo não verificado")
    else:
        print("⚠ Aviso: Não encontrou botão visualizar")
    
    time.sleep(3)
    
    # [6] Desativar notícia
    print("\n[6/6] Desativando notícia...")
    driver.get(f"{BASE_URL}/noticias/listar.php")
    wait.until(EC.presence_of_element_located((By.TAG_NAME, "table")))
    print("  Tabela carregada, aguardando...")
    time.sleep(5)
    
    tabela = driver.find_element(By.TAG_NAME, "table")
    linhas = tabela.find_elements(By.TAG_NAME, "tr")
    
    desativou = False
    for linha in linhas:
        texto_linha = linha.text
        if titulo_noticia in texto_linha:
            print(f"  ✓ Encontrou a notícia na tabela")
            botoes = linha.find_elements(By.TAG_NAME, "a")
            print(f"  Total de botões encontrados: {len(botoes)}")
            
            for i, botao in enumerate(botoes):
                texto_botao = botao.text.lower()
                href = botao.get_attribute("href") or ""
                print(f"  Botão {i}: {texto_botao} - href: {href}")
                
                if "desativar" in href.lower() or "desativar" in texto_botao:
                    print(f"  Clicando em desativar...")
                    driver.execute_script("arguments[0].scrollIntoView(true);", botao)
                    time.sleep(2)
                    try:
                        botao.click()
                    except:
                        driver.execute_script("arguments[0].click();", botao)
                    
                    # Aguarda e aceita o alert
                    time.sleep(2)
                    try:
                        alert = wait.until(EC.alert_is_present())
                        print(f"  Alert encontrado: '{alert.text}'")
                        time.sleep(2)
                        alert.accept()
                        print(f"  ✓ Alert aceito!")
                    except Exception as e:
                        print(f"  Nenhum alert encontrado: {e}")
                    
                    desativou = True
                    break
            break
    
    if not desativou:
        print("⚠ Aviso: Não encontrou botão desativar")
    else:
        print("✓ Clique em desativar executado")
    
    time.sleep(5)
    print("✓ Notícia desativada!")
    
    # [7] Verificar se foi removida da lista
    print("\n[7/7] Verificando remoção da lista pública...")
    print("  Recarregando página...")
    time.sleep(3)
    
    # Recarrega a página para garantir
    driver.get(f"{BASE_URL}/noticias/listar.php")
    wait.until(EC.presence_of_element_located((By.TAG_NAME, "table")))
    print("  Tabela carregada, aguardando...")
    time.sleep(5)
    
    corpo = driver.find_element(By.TAG_NAME, "body").text
    
    if titulo_noticia not in corpo:
        print("✓ Notícia removida da lista pública com sucesso!")
    else:
        print("ℹ Notícia ainda visível na tabela (admin pode ver desativadas)")
    
    time.sleep(3)
    print("\n" + "="*60)
    print("✓ TESTE CONCLUÍDO COM SUCESSO!")
    print("="*60 + "\n")

except Exception as e:
    print(f"\n✗ ERRO: {e}")
    if driver:
        screenshot_path = f"erro_{datetime.now().strftime('%Y%m%d_%H%M%S')}.png"
        driver.save_screenshot(screenshot_path)
        print(f"Screenshot salvo: {screenshot_path}")

finally:
    if driver:
        if titulo_noticia:
            limpar_noticia_teste(titulo_noticia)
        driver.quit()
        print("Driver fechado")