#!/usr/bin/env python
# -*- encoding: utf-8 -*-

import json
import requests
import mysql.connector
import datetime

from bs4 import BeautifulSoup
from mysql.connector import Error


def connMYSQL(var01, var02, var03, var04, var05, var06):
    try:
        str_connection  = mysql.connector.connect(host='10.168.196.187', database='tablero', user='tablero_admin', password='t4bl3r02019')
        str_cursor      = str_connection.cursor(buffered=True)

        str_select      = "SELECT a.impCompra, a.impVenta FROM COTIZACIONDETALLE a WHERE a.codEstado = %s AND a.codCotizacion = %s AND a.codCotizacionTipo = %s"
        str_cursor.execute(str_select, (var01, var02, var03))
        str_row         = str_cursor.fetchall()

        for row in str_row:
            if row[0] != var04 or row[1] != var05:
                str_datetime = datetime.datetime.now()
                str_fecha    = str(str_datetime.year) + '-' + str(str_datetime.month).zfill(2) + '-' + str(str_datetime.day).zfill(2)
                str_hora     = str(str_datetime.hour).zfill(2) + ':' + str(str_datetime.minute).zfill(2) + ':' + str(str_datetime.second).zfill(2)
                str_usuario  = 'SISTEMA'

                str_update = "UPDATE COTIZACIONDETALLE SET codEstado = 'H' WHERE codEstado = %s AND codCotizacion = %s AND codCotizacionTipo = %s"
                str_cursor.execute(str_update, (var01, var02, var03))

                str_insert = "INSERT INTO COTIZACIONDETALLE(codEstado, codCotizacion, codCotizacionTipo, impCompra, impVenta, fecPizarra, fecAlta, horAlta, usuAlta) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)"
                str_cursor.execute(str_insert, (var01, var02, var03, var04, var05, var06, str_hora, str_fecha, str_usuario))

                str_connection.commit()

    except mysql.connector.Error as error:
        print("Errors: {}".format(error))
    finally:
        if (str_connection.is_connected()):
            str_cursor.close()
            str_connection.close()

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

if __name__ == "__main__":
    #MUNDIAL CAMBIOS SUCURSAL VENDOME
    getMundialCambios('http://www.mundialcambios.com.py/json.php', '3', 71, 75, 76, 72, 74, 77, 73)

    #MUNDIAL CAMBIOS SUCURSAL GLOBO CENTER
    getMundialCambios('http://www.mundialcambios.com.py/json.php', '4', 85, 89, 90, 86, 88, 91, 87)