package com.example.control_inventario.data.db.relation

import androidx.room.Embedded
import androidx.room.Relation
import com.example.control_inventario.data.db.entity.Computadora
import com.example.control_inventario.data.db.entity.Laboratorio

data class LaboratorioCompleto(
    @Embedded val laboratorio: Laboratorio,

    @Relation(
        entity = Computadora::class, // Entidad intermedia
        parentColumn = "nombre", // Pk de Laboratorio
        entityColumn = "laboratorioNombre" //Fk en Computadora
    )
    // Room sabe como resolver esta relcion anidada
    val computadoras: List<ComputadoraConAccesorios>
)