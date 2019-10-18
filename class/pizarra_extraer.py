#!/usr/bin/env python
# -*- encoding: utf-8 -*-

import json
import requests
import mysql.connector
import datetime
import decimal

from bs4 import BeautifulSoup
from mysql.connector import Error

def connMYSQL(var01, var02, var03, var04, var05, var06):
    try:
        str_datetime    = datetime.datetime.now()
        str_fecha       = str(str_datetime.year) + '-' + str(str_datetime.month).zfill(2) + '-' + str(str_datetime.day).zfill(2)
        str_hora        = str(str_datetime.hour).zfill(2) + ':' + str(str_datetime.minute).zfill(2) + ':' + str(str_datetime.second).zfill(2)
        str_usuario     = 'SISTEMA'

        str_connection  = mysql.connector.connect(host='10.168.196.187', database='tablero', user='tablero_admin', password='t4bl3r02019')
        str_cursor      = str_connection.cursor(buffered=True)

        if str(str_datetime.hour).zfill(2) == '05' and str(str_datetime.minute).zfill(2) == '00':
            str_update   = "UPDATE COTIZACIONDETALLE SET cssEstado = 'H', cssCompra = 'cotizacion-igual', cssVenta = 'cotizacion-igual' WHERE codEstado = %s AND codCotizacion = %s AND codCotizacionTipo = %s"
            str_cursor.execute(str_update, (var01, var02, var03))

        str_select      = "SELECT a.impCompra, a.impVenta FROM COTIZACIONDETALLE a WHERE a.codEstado = %s AND a.codCotizacion = %s AND a.codCotizacionTipo = %s"
        str_cursor.execute(str_select, (var01, var02, var03))
        str_row         = str_cursor.fetchall()

        for row in str_row:
            if (decimal.Decimal(row[0]) == decimal.Decimal(var04)) and (decimal.Decimal(row[1]) == decimal.Decimal(var05)):
                str_update   = "UPDATE COTIZACIONDETALLE SET cssEstado = 'H' WHERE codEstado = %s AND codCotizacion = %s AND codCotizacionTipo = %s"
                str_cursor.execute(str_update, (var01, var02, var03))
            else:
                css_estado   = 'A'
                css_compra   = formatColor(row[0], decimal.Decimal(var04))
                css_venta    = formatColor(row[1], decimal.Decimal(var05))

                str_update   = "UPDATE COTIZACIONDETALLE SET codEstado = 'H' WHERE codEstado = %s AND codCotizacion = %s AND codCotizacionTipo = %s"
                str_cursor.execute(str_update, (var01, var02, var03))

                str_insert   = "INSERT INTO COTIZACIONDETALLE(codEstado, codCotizacion, codCotizacionTipo, impCompra, impVenta, cssEstado, cssCompra, cssVenta, fecPizarra, fecAlta, horAlta, usuAlta) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"
                str_cursor.execute(str_insert, (var01, var02, var03, var04, var05, css_estado, css_compra, css_venta, var06, str_fecha, str_hora, str_usuario))
                
            str_connection.commit()
    except mysql.connector.Error as error:
        print("Errors: {}".format(error))
    finally:
        if (str_connection.is_connected()):
            str_cursor.close()
            str_connection.close()

def formatColor(var01, var02):
    if var01 == var02:
        return 'cotizacion-igual'
    elif var01 > var02:
        return 'cotizacion-baja'
    elif var01 < var02:
        return 'cotizacion-sube'

def formatNumer(var01):
    return ('%.3f' % var01).rstrip('0').rstrip('.')

def getMundialCambios(var01, var02, var03, var04, var05, var06, var07, var08, var09):
    headers = {
        'Accept': '*/*',
        'Accept-Encoding': 'gzip, deflate',
        'Accept-Language': 'es-ES,es;q=0.9',
        'Connection': 'keep-alive',
        'Content-Length': '4',
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'User-Agent': 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36'
    }

    data = {
        'id': var02
    }

    try:
        response    = requests.post(var01, data, headers=headers, timeout=10).json()

        str_fecha   = response['data']['actualizacion']['0']['fec_fecha_hora']
        str_fecha2  = str_fecha[6:-9] + '-' + str_fecha[3:-14] + '-' + str_fecha[:2] + ' ' +  str_fecha[11:]

        str_compra  = response['data']['cambios']['0']['cot_compra']
        str_venta   = response['data']['cambios']['0']['cot_venta']
        connMYSQL('A', var03, 1, str_compra, str_venta, str_fecha2)

        str_compra  = response['data']['arbitraje']['0']['cot_arb_compra']
        str_venta   = response['data']['arbitraje']['0']['cot_arb_venta']
        connMYSQL('A', var04, 1, str_compra, str_venta, str_fecha2)

        str_compra  = response['data']['arbitraje']['1']['cot_arb_compra']
        str_venta   = response['data']['arbitraje']['1']['cot_arb_venta']
        connMYSQL('A', var05, 1, str_compra, str_venta, str_fecha2)

        str_compra  = response['data']['cambios']['1']['cot_compra']
        str_venta   = response['data']['cambios']['1']['cot_venta']
        connMYSQL('A', var06, 1, str_compra, str_venta, str_fecha2)

        str_compra  = response['data']['cambios']['6']['cot_compra']
        str_venta   = response['data']['cambios']['6']['cot_venta']
        connMYSQL('A', var07, 1, str_compra, str_venta, str_fecha2)

        str_compra  = response['data']['arbitraje']['5']['cot_arb_compra']
        str_venta   = response['data']['arbitraje']['5']['cot_arb_venta']
        connMYSQL('A', var08, 1, str_compra, str_venta, str_fecha2)

        str_compra  = response['data']['cambios']['2']['cot_compra']
        str_venta   = response['data']['cambios']['2']['cot_venta']
        connMYSQL('A', var09, 1, str_compra, str_venta, str_fecha2)

    except requests.ConnectionError:
        response = 0

def getMonedaCambios(var01, var02, var03, var04, var05, var06, var07, var08, var09):
    try:
        response        = requests.get(var01)
        soupResponse    = BeautifulSoup(response.content, 'html.parser')

        if var02 == 1:
            soupContent = soupResponse.find('div', {'id' : 'cotizaciones1'})
        elif var02 == 5:
            soupContent = soupResponse.find('div', {'id' : 'cotizaciones5'})
        
        soupDiv         = soupContent.find('div', {'class' : 'portfolio-item'})
        soupTable       = soupDiv.find('table')
        soupForm        = soupContent.find('form', {'method' : 'post'}).get_text().replace('Última actualización:', '').replace('Las cotizaciones de divisas por Sucursales', '')
        soupFecha       = soupForm.replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
        str_fecha2      = soupFecha[:10] + ' ' + soupFecha[10:]

        for index in range(1, 8):
            soupTr          = soupTable.find_all('tr')[index]
            soupTdMoneda    = soupTr.find_all('td')[1].text

            if soupTdMoneda.upper() == 'DOLAR': 
                str_compra    = soupTr.find_all('td')[2].get_text().replace('.', '').replace(' ', '')
                str_venta     = soupTr.find_all('td')[3].get_text().replace('.', '').replace(' ', '')
                connMYSQL('A', var03, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'DOLAR X REAL':
                str_compra    = soupTr.find_all('td')[2].get_text().replace(',', '.').replace(' ', '')
                str_venta     = soupTr.find_all('td')[3].get_text().replace(',', '.').replace(' ', '')
                connMYSQL('A', var04, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'DOLAR X PESOS':
                str_compra    = soupTr.find_all('td')[2].get_text().replace(',', '.').replace(' ', '')
                str_venta     = soupTr.find_all('td')[3].get_text().replace(',', '.').replace(' ', '')
                connMYSQL('A', var05, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'REAL':
                str_compra    = soupTr.find_all('td')[2].get_text().replace('.', '').replace(' ', '')
                str_venta     = soupTr.find_all('td')[3].get_text().replace('.', '').replace(' ', '')
                connMYSQL('A', var06, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'EURO':
                str_compra    = soupTr.find_all('td')[2].get_text().replace('.', '').replace(' ', '')
                str_venta     = soupTr.find_all('td')[3].get_text().replace('.', '').replace(' ', '')
                connMYSQL('A', var07, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'DOLAR X EURO':
                str_compra    = soupTr.find_all('td')[2].get_text().replace(',', '.').replace(' ', '')
                str_venta     = soupTr.find_all('td')[3].get_text().replace(',', '.').replace(' ', '')
                connMYSQL('A', var08, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'PESOS':
                str_compra    = soupTr.find_all('td')[2].get_text().replace('.', '').replace(' ', '')
                str_venta     = soupTr.find_all('td')[3].get_text().replace('.', '').replace(' ', '')
                connMYSQL('A', var09, 1, str_compra, str_venta, str_fecha2)
        else:
            print("Finally finished!")
    except requests.ConnectionError:
        response    = 0

def getMazzaHnos(var01, var02, var03, var04, var05, var06):
    try:
        response        = requests.get(var01)
        soupResponse    = BeautifulSoup(response.content, 'html.parser')
        soupContent     = soupResponse.find('div', {'id' : 'contenedor90'})
        
        str_fecha       = soupContent.find('div', {'id' : 'ult_actualizacion2'}).get_text().replace('Ultima Actualizacion ', '')
        str_fecha2      = str_fecha[6:-6] + '-' + str_fecha[3:-11] + '-' + str_fecha[:2] + ' ' +  str_fecha[11:]

        for index in range(0, 5):
            soupDiv         = soupContent.find_all('div', {'id' : 'item'})[index]
            str_moneda      = soupDiv.find_all('div')[1].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')

            if str_moneda.upper() == 'DOLAREE.UU':
                str_compra      = soupDiv.find_all('div')[2].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                str_venta       = soupDiv.find_all('div')[3].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                connMYSQL('A', var02, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'EURO':
                str_compra      = soupDiv.find_all('div')[2].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                str_venta       = soupDiv.find_all('div')[3].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                connMYSQL('A', var03, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'REAL':
                str_compra      = soupDiv.find_all('div')[2].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                str_venta       = soupDiv.find_all('div')[3].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                connMYSQL('A', var04, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'GUARANI/PESO':
                str_compra      = soupDiv.find_all('div')[2].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                str_venta       = soupDiv.find_all('div')[3].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                connMYSQL('A', var05, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'DOLAR/REAL':
                str_compra      = soupDiv.find_all('div')[2].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                str_venta       = soupDiv.find_all('div')[3].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
                connMYSQL('A', var06, 1, str_compra, str_venta, str_fecha2)
        else:
            print("Finally finished!")
    except requests.ConnectionError:
        response    = 0

def get2Arroyos(var01, var02, var03, var04, var05, var06, var07, var08, var09):
    try:
        response        = requests.get(var01)
        soupResponse    = BeautifulSoup(response.content, 'html.parser')

        if var02 == 1:
            soupContent     = soupResponse.find('div', {'id' : 'Posadas'})
            indexRange      = 7
        elif var02 == 2:
            soupContent     = soupResponse.find('div', {'id' : 'Formosa'})
            indexRange      = 6

        soupTable       = soupContent.find('table')
        soupTBody       = soupTable.find('tbody')

        for index in range(0, indexRange):
            soupTr          = soupTBody.find_all('tr')[index]

            str_moneda      = soupTr.find_all('td')[0].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
            str_compra      = soupTr.find_all('td')[1].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '').replace(',', '.')
            str_venta       = soupTr.find_all('td')[2].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '').replace(',', '.')
            str_datetime    = datetime.datetime.now()
            str_fecha2      = str(str_datetime.year) + '-' + str(str_datetime.month).zfill(2) + '-' + str(str_datetime.day).zfill(2) + ' ' + str(str_datetime.hour).zfill(2) + ':' + str(str_datetime.minute).zfill(2)
            
            if str_moneda.upper() == 'DÓLAR':
                connMYSQL('A', var03, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'REAL':
                connMYSQL('A', var04, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'EURO':
                connMYSQL('A', var05, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'GUARANÍ':
                connMYSQL('A', var06, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'DÓLARXREAL':
                connMYSQL('A', var07, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'EUROXDÓLAR':
                connMYSQL('A', var08, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'DÓLARXGUARANÍ':
                connMYSQL('A', var09, 1, str_compra, str_venta, str_fecha2)

        else:
            print("Finally finished!")
    except requests.ConnectionError:
        response    = 0

def getBonanzaCambios(var01, var02, var03, var04, var05, var06, var07, var08):
    try:
        response        = requests.get(var01)
        soupResponse    = BeautifulSoup(response.content, 'html.parser')
        soupSection     = soupResponse.find('section', {'class' : {'flat-tab-services'}})

        str_datetime    = datetime.datetime.now()
        str_fecha2      = str(str_datetime.year) + '-' + str(str_datetime.month).zfill(2) + '-' + str(str_datetime.day).zfill(2) + ' ' + str(str_datetime.hour).zfill(2) + ':' + str(str_datetime.minute).zfill(2)
        
        soupDivCambio   = soupSection.find_all('div', {'class' : {'one-half'}})[0]
        soupCambio      = soupDivCambio.find('section', {'class' : {'flat-pricing style1'}})
        
        for index in range(1, 2):
            soupTr          = soupCambio.find_all('tr')[index]

            str_moneda      = soupTr.find_all('td', {'class' : {'title'}})[0].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
            str_compra      = soupTr.find_all('td', {'class' : {'moneda'}})[0].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '').replace('.', '')
            str_venta       = soupTr.find_all('td', {'class' : {'moneda'}})[1].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '').replace('.', '')

            if str_moneda.upper() == 'DOLARAMERICANO':
                connMYSQL('A', var02, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'REAL':
                connMYSQL('A', var03, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'EURO':
                connMYSQL('A', var04, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'PESOARGENTINO':
                connMYSQL('A', var05, 1, str_compra, str_venta, str_fecha2)

        else:
            print("Finally finished!")
        

        soupArbitraje   = soupResponse.find('section', {'class' : {'flat-pricing stylearbi'}})
        soupTBody       = soupArbitraje.find('tbody')
        
        for index in range(0, 3):
            soupRow         = soupTBody.find_all('tr')[index]
            str_moneda      = soupRow.find('td', {'class' : 'title'}).get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '')
            str_compra      = soupRow.find_all('td', {'class' : 'moneda'})[0].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '').replace(',', '.')
            str_venta       = soupRow.find_all('td', {'class' : 'moneda'})[1].get_text().replace('\n', '').replace('\r', '').replace('\t', '').replace(' ', '').replace(',', '.')
            
            if str_moneda.upper() == 'DolarAmericanoxEuro'.upper():
                connMYSQL('A', var06, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'DolarAmericanoxPesoArgentino'.upper():
                connMYSQL('A', var07, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'DolarAmericanoxReal'.upper():
                connMYSQL('A', var08, 1, str_compra, str_venta, str_fecha2)
        else:
            print("Finally finished!")

    except requests.ConnectionError:
        response    = 0

def getCambiosChaco(var01, var02, var03, var04, var05, var06, var07, var08):
    try:
        response    = requests.get(var01, timeout=10).json()

        for data in response['items']:
            str_fecha   = data['updatedAt']
            str_fecha2  = str_fecha[:-8].replace('T', ' ')

            str_moneda  = data['isoCode']
            str_compra  = formatNumer(data['purchasePrice'])
            str_venta   = formatNumer(data['salePrice'])
            arb_compra  = formatNumer(data['purchaseArbitrage'])
            arb_venta   = formatNumer(data['saleArbitrage'])

            if str_moneda.upper() == 'USD':
                connMYSQL('A', var02, 1, str_compra, str_venta, str_fecha2)

            elif str_moneda.upper() == 'BRL':
                connMYSQL('A', var03, 1, str_compra, str_venta, str_fecha2)
                connMYSQL('A', var04, 1, arb_compra, arb_venta, str_fecha2)

            elif str_moneda.upper() == 'ARS':
                connMYSQL('A', var05, 1, str_compra, str_venta, str_fecha2)
                connMYSQL('A', var06, 1, arb_compra, arb_venta, str_fecha2)

            elif str_moneda.upper() == 'EUR':
                connMYSQL('A', var07, 1, str_compra, str_venta, str_fecha2)
                connMYSQL('A', var08, 1, arb_compra, arb_venta, str_fecha2)
    except requests.ConnectionError:
        response = 0

def getMercosurCambios(var01, var02, var03, var04, var05, var06, var07, var08):
    try:
        response        = requests.get(var01)
        soupResponse    = BeautifulSoup(response.content, 'html.parser')
        soupContent     = soupResponse.find('div', {'class' : 'tab_panels'})
        soupDiv         = soupContent.find_all('div', {'class' : 'tab_panel description active'})
        soupTable       = soupDiv[0].find('table')
        soupTBody       = soupTable.find('tbody')

        str_datetime    = datetime.datetime.now()
        str_fecha2      = str(str_datetime.year) + '-' + str(str_datetime.month).zfill(2) + '-' + str(str_datetime.day).zfill(2) + ' ' + str(str_datetime.hour).zfill(2) + ':' + str(str_datetime.minute).zfill(2)
        

        for index in range(0, 9):
            soupTr          = soupTBody.find_all('tr')[index]
            soupTdMoneda    = soupTr.find_all('th')[1].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')

            if soupTdMoneda.upper() == 'DólarxGuarani'.upper():
                str_compra    = soupTr.find_all('th')[3].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                str_venta     = soupTr.find_all('th')[4].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                connMYSQL('A', var02, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'DólarxReal'.upper():
                str_compra    = soupTr.find_all('th')[3].get_text().replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                str_venta     = soupTr.find_all('th')[4].get_text().replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                connMYSQL('A', var03, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'DólarxPeso'.upper():
                str_compra    = soupTr.find_all('th')[3].get_text().replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                str_venta     = soupTr.find_all('th')[4].get_text().replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                connMYSQL('A', var04, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'RealxGuarani'.upper():
                str_compra    = soupTr.find_all('th')[3].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                str_venta     = soupTr.find_all('th')[4].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                connMYSQL('A', var05, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'EuroxGuarani'.upper():
                str_compra    = soupTr.find_all('th')[3].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                str_venta     = soupTr.find_all('th')[4].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                connMYSQL('A', var06, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'EuroxDólar'.upper():
                str_compra    = soupTr.find_all('th')[3].get_text().replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                str_venta     = soupTr.find_all('th')[4].get_text().replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                connMYSQL('A', var07, 1, str_compra, str_venta, str_fecha2)

            elif soupTdMoneda.upper() == 'PesoxGuarani'.upper():
                str_compra    = soupTr.find_all('th')[3].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                str_venta     = soupTr.find_all('th')[4].get_text().replace('.', '').replace(' ', '').replace('\n', '').replace('\r', '').replace('\t', '')
                connMYSQL('A', var08, 1, str_compra, str_venta, str_fecha2)

        else:
            print("Finally finished!")

    except requests.ConnectionError:
        response    = 0

if __name__ == "__main__":
    #MUNDIAL CAMBIOS SUCURSAL VENDOME
    getMundialCambios('http://www.mundialcambios.com.py/json.php', '3', 71, 75, 76, 72, 74, 77, 73)

    #MUNDIAL CAMBIOS SUCURSAL GLOBO CENTER
    getMundialCambios('http://www.mundialcambios.com.py/json.php', '4', 85, 89, 90, 86, 88, 91, 87)

    #MUNDIAL CAMBIOS SUCURSAL KM4
    getMundialCambios('http://www.mundialcambios.com.py/json.php', '2', 43, 47, 48, 44, 46, 49, 45)

    #LA MONEDA CAMBIOS SUCURSAL CENTRO
    getMonedaCambios('http://www.lamoneda.com.py', 1, 29, 33, 34, 30, 32, 35, 31)

    #LA MONEDA CAMBIOS SUCURSAL JEBAI
    getMonedaCambios('http://www.lamoneda.com.py', 5, 176, 180, 181, 177, 179, 182, 178)

    #MAZZA HNOS SUCURSAL CENTRO
    getMazzaHnos('http://www.mazzahnos.com.ar/cotizaciones.php?sucursal=2', 55, 56, 51, 52, 54)

    #MAZZA HNOS CASA CENTRAL
    getMazzaHnos('http://www.mazzahnos.com.ar/cotizaciones.php?sucursal=1', 188, 189, 184, 185, 187)

    #2 ARROYOS POSADA
    get2Arroyos('https://www.dosarroyoscambios.com', 1, 62, 58, 60, 59, 61, 63, 57)

    #2 ARROYOS FORMOSA
    get2Arroyos('https://www.dosarroyoscambios.com', 2, 195, 191, 193, 192, 194, 196, 190)

    #CAMBIOS CHACO ADRIAN JARA
    getCambiosChaco('http://www.cambioschaco.com.py/api/branch_office/9/exchange', 15, 16, 19, 17, 20, 18, 21)

    #CAMBIOS CHACO MONSEÑOR RODRIGUEZ
    getCambiosChaco('http://www.cambioschaco.com.py/api/branch_office/32/exchange', 169, 170, 173, 171, 174, 172, 175)

    #BONANZA CAMBIOS CASA CENTRAL
    getBonanzaCambios('http://www.bonanzacambios.com.py/index.php', 36, 37, 39, 38, 42, 41, 40)

    #MUNDIALCAMBIOS MATRIZ
    getMercosurCambios('http://2019.mercosurcambios.com/', 92, 96, 97, 93, 95, 98, 94)