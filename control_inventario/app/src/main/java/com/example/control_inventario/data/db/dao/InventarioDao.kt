package com.example.control_inventario.data.db.dao

import androidx.room.*
import com.example.control_inventario.data.db.entity.Accesorio
import com.example.control_inventario.data.db.entity.Computadora
import com.example.control_inventario.data.db.entity.Laboratorio
import com.example.control_inventario.data.db.relation.LaboratorioCompleto
import kotlinx.coroutines.flow.Flow

@Dao
interface InventarioDao {
    // Metodo de Insercion
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertLaboratorio(lab: Laboratorio)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertComputadora(lab: Computadora)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun insertAccesorio(lab: Accesorio)

    // Metodo de consulta (Reactivos con Flow)
    // Obtener la lista completa de laboratorios con sus computadoeas y accesorios
    @Transaction
    @Query("SELECT * FROM laboratorios ORDER BY nombre ASC")
    fun getInvetarioCompleto(): Flow<List<LaboratorioCompleto>>

    // Obtener todos los laboratorios
    @Query("SELECT * FROM laboratorios")
    fun getAllLaboratorios(): Flow<List<Laboratorio>>

    // Metodo de Eliminacion
    @Delete
    suspend fun deleteLaboratorio(lab: Laboratorio)

    @Delete
    suspend fun deleteComputadora(pc: Computadora)

    @Query("DELETE FROM computadoras WHERE codigo= :codigoPc")
    suspend fun deleteComputadoraPorCodigo(codigoPc: String)
}