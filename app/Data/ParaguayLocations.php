<?php

namespace App\Data;

class ParaguayLocations
{
    public static function departments(): array
    {
        return [
            ['id' => 'Asunción', 'name' => 'Asunción (Capital)', 'districts' => ['Asunción']],
            ['id' => 'Concepción', 'name' => 'Concepción', 'districts' => ['Concepción','Belén','Horqueta','Loreto','San Carlos del Apa','San Lázaro','Yby Yaú','Azotey','Sargento José Félix López','San Alfredo','Paso Barreto']],
            ['id' => 'San Pedro', 'name' => 'San Pedro', 'districts' => ['San Pedro de Ycuamandiyú','Antequera','Choré','General Elizardo Aquino','Itacurubí del Rosario','Liberación','Lima','Nueva Germania','San Estanislao','San Pablo','Tacuatí','Unión','25 de Diciembre','Villa del Rosario','General Resquín','Yataity del Norte','Guayaibí','Capiibary','Santa Rosa del Aguaray']],
            ['id' => 'Cordillera', 'name' => 'Cordillera', 'districts' => ['Caacupé','Altos','Arroyos y Esteros','Atyrá','Caraguatay','Emboscada','Eusebio Ayala','Isla Pucú','Itacurubí de la Cordillera','Juan de Mena','Loma Grande','Mbocayaty del Yhaguy','Nueva Colombia','Piribebuy','Primero de Marzo','San Bernardino','San José Obrero','Santa Elena','Tobatí','Valenzuela']],
            ['id' => 'Guairá', 'name' => 'Guairá', 'districts' => ['Villarrica','Borja','Capitán Mauricio José Troche','Coronel Martínez','Doctor Bottrell','Félix Pérez Cardozo','General Eugenio A. Garay','Independencia','Itapé','Iturbe','José Fassardi','Mbocayaty','Natalicio Talavera','Ñumí','Paso Yobái','San Salvador','Yataity','Tebicuary']],
            ['id' => 'Caaguazú', 'name' => 'Caaguazú', 'districts' => ['Coronel Oviedo','Caaguazú','Carayaó','Doctor Cecilio Báez','Santa Rosa del Mbutuy','Doctor Juan Manuel Frutos','Repatriación','Nueva Londres','San Joaquín','San José de los Arroyos','Yhú','José Domingo Ocampos','Mcal. Francisco Solano López','La Pastora','3 de Febrero','Simón Bolívar','Raúl Arsenio Oviedo','Juan Esteban O\'Leary','R.I. 3 Corrales','Vaquería','Tembiaporã']],
            ['id' => 'Caazapá', 'name' => 'Caazapá', 'districts' => ['Caazapá','Abaí','Buena Vista','Doctor Moisés Bertoni','General Higinio Morínigo','Maciel','San Juan Nepomuceno','Tavaí','Yuty','3 de Mayo','Fulgencio Yegros']],
            ['id' => 'Itapúa', 'name' => 'Itapúa', 'districts' => ['Encarnación','Bella Vista','Cambyretá','Capitán Meza','Capitán Miranda','Nueva Alborada','Carmen del Paraná','Coronel Bogado','Edelira','Fram','General Artigas','General Delgado','Hohenau','Jesús','José Leandro Oviedo','La Paz','Mayor Otaño','Natalio','Obligado','Pirapó','San Cosme y Damián','San Juan del Paraná','San Pedro del Paraná','San Rafael del Paraná','Tomás Romero Pereira','Trinidad','Yatytay','Alto Verá','Itapúa Poty','Carlos Antonio López']],
            ['id' => 'Misiones', 'name' => 'Misiones', 'districts' => ['San Juan Bautista','Ayolas','San Ignacio','San Miguel','San Patricio','Santa María','Santa Rosa','Santiago','Villa Florida','Yabebyry']],
            ['id' => 'Paraguarí', 'name' => 'Paraguarí', 'districts' => ['Paraguarí','Acahay','Caapucú','Caballero','Carapeguá','Escobar','General Bernardino Caballero','La Colmena','Mbuyapey','Pirayú','Quiindy','Quyquyhó','San Roque González de Santa Cruz','Sapucai','Tebicuarymí','Yaguarón','Ybycuí','Ybytymí']],
            ['id' => 'Alto Paraná', 'name' => 'Alto Paraná', 'districts' => ['Ciudad del Este','Doctor Juan León Mallorquín','Domingo Martínez de Irala','Hernandarias','Iruña','Itakyry','Juan Emilio O\'Leary','Los Cedrales','Mbaracayú','Minga Guazú','Minga Porã','Ñacunday','Naranjal','Presidente Franco','San Alberto','San Cristóbal','Santa Fe del Paraná','Santa Rita','Santa Rosa del Monday','Yguazú','Tavapy']],
            ['id' => 'Central', 'name' => 'Central', 'districts' => ['Areguá','Capiatá','Fernando de la Mora','Guarambaré','Itá','Itauguá','Lambaré','Limpio','Luque','Mariano Roque Alonso','Ñemby','Nueva Italia','San Antonio','San Lorenzo','Villa Elisa','Villeta','Ypacaraí','Ypané','J. Augusto Saldívar']],
            ['id' => 'Ñeembucú', 'name' => 'Ñeembucú', 'districts' => ['Pilar','Alberdi','Cerrito','Desmochados','General José Eduvigis Díaz','Guazú Cuá','Humaitá','Isla Umbú','Laureles','Mayor José J. Martínez','Paso de Patria','San Juan Bautista de Ñeembucú','Tacuaras','Villa Franca','Villa Oliva','Villalbín']],
            ['id' => 'Amambay', 'name' => 'Amambay', 'districts' => ['Pedro Juan Caballero','Bella Vista Norte','Capitán Bado','Karapaí','Zanja Pytã']],
            ['id' => 'Canindeyú', 'name' => 'Canindeyú', 'districts' => ['Salto del Guairá','Corpus Christi','Curuguaty','General Francisco Caballero Álvarez','Itanará','Katueté','La Paloma','Nueva Esperanza','Villa Ygatimí','Yasy Cañy','Yby Pytã','Ypé Jhú','Ybyrarobana']],
            ['id' => 'Presidente Hayes', 'name' => 'Presidente Hayes', 'districts' => ['Villa Hayes','Benjamín Aceval','Nanawa','José Falcón','Puerto Pinasco','Tte. 1° Manuel Irala Fernández','Gral. José María Bruguez','Tte. Esteban Martínez']],
            ['id' => 'Boquerón', 'name' => 'Boquerón', 'districts' => ['Filadelfia','Loma Plata','Mariscal Estigarribia']],
            ['id' => 'Alto Paraguay', 'name' => 'Alto Paraguay', 'districts' => ['Fuerte Olimpo','Bahía Negra','Carmelo Peralta','Puerto Casado']],
        ];
    }

    public static function departmentNames(): array
    {
        return array_column(self::departments(), 'name', 'id');
    }

    public static function districtsByDepartment(string $department): array
    {
        foreach (self::departments() as $dept) {
            if ($dept['id'] === $department) {
                return $dept['districts'];
            }
        }
        return [];
    }
}
