package com.example.control_inventario.data.db.relation

import androidx.room.Embedded
import androidx.room.Relation
import com.example.control_inventario.data.db.entity.Accesorio
import com.example.control_inventario.data.db.entity.Computadora

data class ComputadoraConAccesorios(
    @Embedded val computadora: Computadora,

    @Relation(
        parentColumn = "codigo", // PK e computadora
        entityColumn = "computadoraCodigo" // FK en Accesorio
    )
    val accesorios: List<Accesorio>
)