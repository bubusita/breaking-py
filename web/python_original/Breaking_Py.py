from tabla_periodica import elementos
from datos import Datos
import random
grupos = {
    "1": ["1", "metales alcalinos", "grupo 1"],
    "2": ["2", "metales alcalinotérreos", "grupo 2"],
    "3": ["3", "escandio y el grupo del itrio", "escandio", "grupo del itrio", "itrio", "grupo 3", "actínidos"],
    "4": ["4", "grupo del titanio", "titanio", "grupo 4"],
    "5": ["5", "grupo del vanadio", "vanadio", "grupo 5"],
    "6": ["6", "grupo del cromo", "cromo", "grupo 6"],
    "7": ["7", "grupo del manganeso", "manganeso", "grupo 7"],
    "8": ["8", "grupo del hierro", "hierro", "grupo 8"],
    "9": ["9", "grupo del cobalto", "cobalto", "grupo 9"],
    "10": ["10", "grupo del níquel", "níquel", "grupo 10"],
    "11": ["11", "metales de acuñación", "cobre", "plata", "oro", "grupo 11"],
    "12": ["12", "grupo del zinc", "zinc", "grupo 12"],
    "13": ["13", "térreos", "grupo del boro", "boro", "grupo 13"],
    "14": ["14", "carbonoides", "grupo del carbono", "carbono", "grupo 14"],
    "15": ["15", "nitrogenoides", "grupo del nitrógeno", "nitrógeno", "grupo 15"],
    "16": ["16", "calcógenos", "grupo del oxígeno", "oxígeno", "grupo 16"],
    "17": ["17", "halógenos", "grupo 17"],
    "18": ["18", "gases nobles", "grupo 18"]
}
grupo_encontrado = None

def quitar_tildes(texto):
          texto = texto.lower()
          texto = texto.replace("á", "a")
          texto = texto.replace("é", "e")
          texto = texto.replace("í", "i")
          texto = texto.replace("ó", "o")
          texto = texto.replace("ú", "u")
          return texto

False == "no"
True == "si"
score = 0
def quiz():
    try:
        elemento_al_azar= random.choice(list(elementos.keys()))
        global score
        print("¡Empecemos con el quiz! Se eligirá un elemento random y se le tendrá que escribir sus caracteristicas, por cada respuesta bien recibirá un punto y por cada erronea se le restara un punto a ese puntaje."
        "\n" \
        "Escribe las características de este elemento:", elemento_al_azar)
        
        primera_pregunta = input("\nNumero Atómico: ")
        segunda_pregunta = input("Simbolo: ")
        tercera_pregunta = input("Masa Atomica: ")
        cuarta_pregunta = input("Grupo (poner el nombre si es lantánidos o actínidos, no el número): ")
        quinta_pregunta = input("Periodo: ")
        sexta_pregunta = input("Bloque: ")
        septima_pregunta = input("Radiactividad (si/no): ")
        print("\nResultados:")
        
        datos = elementos[elemento_al_azar]
        masa_atomica = float(datos["MasaAtomica"])
        Masa_atomica = masa_atomica // 1
        if quitar_tildes(primera_pregunta.lower()) == str(datos["NumeroAtomico"]).strip():
            print("\nNúmero Atómico: Correcto!")
            score +=1
        else:
            print("Número Atómico: Incorrecto")
            score -=1
        if quitar_tildes(segunda_pregunta.lower()) == str(datos["Simbolo"]).strip().lower():
            print("Simbolo: Correcto!")
            score +=1
        else:
            print("Simbolo: Incorrecto")
            score -=1
        if quitar_tildes(tercera_pregunta.lower()) == str(int(Masa_atomica)).strip().lower():
            print("Masa atomica: Correcto!")
            score +=1
        else:
            print("Masa atomica: Incorrecto")
            score -=1
        if quitar_tildes(cuarta_pregunta.lower()) == str(datos["Grupo"]).strip().lower():
            print("Grupo: Correcto!")
            score +=1
        else:
            print("Grupo: Incorrecto")
            score -=1
        if quitar_tildes(quinta_pregunta.lower()) == str(datos["Periodo"]).strip().lower():
            print("Periodo: Correcto!")
            score +=1
        else:
            print("Periodo: Incorrecto")
            score -=1
        if quitar_tildes(sexta_pregunta.lower()) == str(datos["Bloque"]).strip().lower():
            print("Bloque: Correcto!")
            score +=1
        else:
            print("Bloque: Incorrecto")
            score -=1
        respuesta_si_no = septima_pregunta.strip().lower()
        es_radiactivo = datos["Radiactivo"]
        if (respuesta_si_no == "si" and es_radiactivo) or (respuesta_si_no == "no" and not es_radiactivo):
            print("Radioactividad: Correcta!")
            score +=1
        else:
            septima_pregunta = False
            print("Radioactividad: Incorrecta")
            score -=1
        print (score)
        respuesta_f = input("\nQuiere jugar de nuevo? (si/no): ").lower().strip()
        if respuesta_f == "si":
            quiz()
        if respuesta_f == "no":
            print("ok!")
            codigo()
    except ValueError:
        ("Parece que hubo un error en el codigo, porque no lo intentas de nuevo ;)")
        codigo()
def codigo():
    while True:
        try:
            print ("\nQuiere poner un elemento, una caracteristica, un quiz o datos curiosos? ")
            print ("[1] Característica")
            print ("[2] Elemento")
            print ("[3] Quiz")
            print ("[4] Datos curiosos")
            elemento = "no es un elemento"
            primera_elec = input("\nelige una opcion numerica:")
            if primera_elec == "1":
                # Convertimos el diccionario en una lista filtrable
                lista_filtrada = list(elementos.values())

                # Características disponibles (clave: descripción, valor: identificador interno)
                opciones = {
                "1": "radioactivo",
                "2": "grupo",
                "3": "periodo",
                "4": "bloque",
                "5": "masa"
                }

                usadas = []  # Aquí guardamos qué filtros ya se usaron
                PrimeraVez = True
                while True:
                    if PrimeraVez == False:
                        seguir = input("\n¿Querés seguir filtrando? (si/no): ").lower()
                        if seguir == "no":
                            print("\nFiltrado terminado.")
                            codigo()
                        elif seguir != "si":
                            print("Respuesta inválida. Se toma como 'no'.")
                            return codigo()

                    print("\n¿Qué característica querés usar para filtrar?")
                    PrimeraVez = False
                
                    # Mostrar solo las opciones que NO fueron utilizadas
                    for clave, nombre in opciones.items():
                        if nombre not in usadas:
                            print(f"[{clave}] {nombre.capitalize()}")
                    print("[6] Terminar filtrado")

                    eleccion = input("\nelige una opcion numerica:")

                    # Si quiere terminar
                    if eleccion == "6":
                        print ("\nvamos de nuevo :D")
                        codigo()

                    # Si elige algo que ya usó, no lo dejamos
                    if eleccion not in opciones or opciones[eleccion] in usadas:
                        print("Esa característica ya fue usada o no existe.")
                        continue

                    # Marcar la característica como usada
                    usadas.append(opciones[eleccion])
                    caracteristica = opciones[eleccion]

                    # Aplicar el filtro correspondiente
                    if caracteristica == "radioactivo":
                        valor = input("¿Radioactivo? (si/no): ").lower()
                        lista_filtrada = [el for el in lista_filtrada if el["Radiactivo"] == (valor == "si")]
                        print(f"\nSe imprimio/eron {len(lista_filtrada)} elementos con esta condición.\n")
                    elif caracteristica == "grupo":
                        valor = int(input("Número de grupo: "))
                        lista_filtrada = [el for el in lista_filtrada if el["Grupo"] == valor]
                        print(f"\nSe imprimio/eron {len(lista_filtrada)} elementos con esta condición.\n")
                    elif caracteristica == "periodo":
                        valor = int(input("Número de periodo: "))
                        lista_filtrada = [el for el in lista_filtrada if el["Periodo"] == valor]
                        print(f"\nSe imprimio/eron {len(lista_filtrada)} elementos con esta condición.\n")
                    elif caracteristica == "bloque":
                        valor = input("Bloque (s, p, d, f): ").lower()
                        lista_filtrada = [el for el in lista_filtrada if el["Bloque"] == valor]
                        print(f"\nSe imprimio/eron {len(lista_filtrada)} elementos con esta condición.\n")
                    elif caracteristica == "masa":
                        print ("/nNivel de masa atómica")
                        print ("[1] Masas bajas (1-20 u)")
                        print ("[2] Masas intermedias (21-100)")
                        print ("[3] Masas altas (101-200)")
                        print ("[4] Masas muy altas (>200)")
                        respuesta = int(input("\nelige una opcion numerica:"))
                        match respuesta:
                            case 1:
                                lista_filtrada = [
                                    el for el in lista_filtrada
                                        if 1 <= el["MasaAtomica"] <= 20
                                ]
                            case 2:
                                lista_filtrada = [
                                    el for el in lista_filtrada
                                    if 21 <= el["MasaAtomica"] <= 100
                                ]
                            case 3:
                                lista_filtrada = [
                                    el for el in lista_filtrada
                                    if 101 <= el["MasaAtomica"] <= 200
                                ]
                            case 4:
                                lista_filtrada = [
                                    el for el in lista_filtrada
                                    if el["MasaAtomica"] > 200
                                ]
                        print(f"\nSe imprimió/eron {len(lista_filtrada)} elementos con esta condición.\n")
                    # Si la lista queda vacía → no seguimos
                    if not lista_filtrada:
                        print("\nNo queda ningún elemento con esas condiciones.")

                    # Mostrar progreso parcial
                    print("\nElementos que cumplen hasta ahora:")
                    for el in lista_filtrada:
                        print(f"- {el['Nombre']}")
            elif primera_elec == "2": 
                def segundoOp():
                    elemento = input("\nIngrese el nombre de un elemento: ")
                    elemento = quitar_tildes(elemento.capitalize())
                    if elemento in elementos:
                        datos = elementos[elemento]
                        for clave, valor in datos.items():   
                            print(f"{clave}: {valor}") # Imprimir en filas
                        print ("Qiere elegir otra caracteristica, terminar el codigo o ir al inicio?")
                        print("[1] Otro elemento")
                        print("[2] Inicio")
                        ter_respuesta = input("\nIngrese la opcion numerica:")
                        if ter_respuesta == "1":
                            segundoOp()
                        if ter_respuesta == "2":
                            codigo()
                    else:
                        print("El elemento no fue encontrado")
                        segundoOp()
                segundoOp()
            elif primera_elec == "3":
                quiz()
            elif primera_elec == "4":
                Datos()
                print("\nLe gustaria otro dato o volver al inicio?")
                print("[1] Otro dato")
                print("[2] Inicio")
                print("[3] Terminar el programa")
                ter_respuesta = input("\nIngrese la opcion numerica:")
                if ter_respuesta == "1":
                    Datos()
                if ter_respuesta == "2":
                    codigo()
                if ter_respuesta == "3":
                    break
            else:
                print ("Esa no es una opción")
                codigo()
        except ValueError:
            print ("Hubo un error, empecemos de nuevo :D")
            return codigo ()
codigo()