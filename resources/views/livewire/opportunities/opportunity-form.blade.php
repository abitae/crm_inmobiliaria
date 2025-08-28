<div>
    <flux:modal wire:model="showModal" size="4xl">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $isEditing ? 'Editar Oportunidad' : 'Nueva Oportunidad' }}
            </h3>
            <flux:button icon="x-mark" size="xs" variant="outline" wire:click="closeModal">
            </flux:button>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <!-- Primera fila: Cliente y Proyecto -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:select label="Cliente *" size="xs" wire:model="client_id">
                        <option value="">Seleccionar cliente</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">
                                {{ $client->name }} - {{ $client->email }}
                            </option>
                        @endforeach
                    </flux:select>
                    @error('client_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <flux:select label="Proyecto *" size="xs" wire:model="project_id">
                        <option value="">Seleccionar proyecto</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </flux:select>
                    @error('project_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Segunda fila: Unidad y Asesor -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                 <div>
                     <flux:select label="Unidad *" size="xs" wire:model="unit_id">
                         <option value="">Seleccionar unidad</option>
                         @foreach ($units as $unit)
                             <option value="{{ $unit->id }}">{{ $unit->name }} - {{ $unit->type }}</option>
                         @endforeach
                     </flux:select>
                     @error('unit_id')
                         <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                     @enderror
                 </div>

                <div>
                    <flux:select label="Asesor Asignado *" size="xs" wire:model="advisor_id">
                        <option value="">Seleccionar asesor</option>
                        @foreach ($advisors as $advisor)
                            <option value="{{ $advisor->id }}">{{ $advisor->name }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <!-- Tercera fila: Etapa y Estado -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:select label="Etapa *" size="xs" wire:model="stage">
                        <option value="captado">Captado</option>
                        <option value="calificado">Calificado</option>
                        <option value="contacto">Contacto</option>
                        <option value="propuesta">Propuesta</option>
                        <option value="visita">Visita</option>
                        <option value="negociacion">Negociación</option>
                        <option value="cierre">Cierre</option>
                    </flux:select>
                </div>

                <div>
                    <flux:select label="Estado *" size="xs" wire:model="status">
                        <option value="activa">Activa</option>
                        <option value="ganada">Ganada</option>
                        <option value="perdida">Perdida</option>
                        <option value="cancelada">Cancelada</option>
                    </flux:select>
                </div>
            </div>

            <!-- Cuarta fila: Probabilidad y Valor Esperado -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input label="Probabilidad de Cierre (%) *" size="xs" type="number"
                        wire:model="probability" min="0" max="100" />
                </div>

                <div>
                    <flux:input label="Valor Esperado (S/) *" size="xs" type="number" wire:model="expected_value"
                        min="0" step="0.01" />
                </div>
            </div>

            <!-- Quinta fila: Fecha de Cierre y Origen -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input label="Fecha de Cierre Esperada *" size="xs" type="date"
                        wire:model="expected_close_date" />
                </div>

                <div>
                    <flux:select label="Origen" size="xs" wire:model="source">
                        <option value="">Seleccionar origen</option>
                        <option value="website">Sitio web</option>
                        <option value="referral">Referido</option>
                        <option value="social">Redes sociales</option>
                        <option value="walkin">Visita directa</option>
                        <option value="cold_call">Llamada en frío</option>
                        <option value="feria">Feria</option>
                        <option value="publicidad">Publicidad</option>
                    </flux:select>
                </div>
            </div>

            <!-- Sexta fila: Campaña -->
            <div>
                <flux:input label="Campaña" size="xs" wire:model="campaign" placeholder="Nombre de la campaña">
                </flux:input>
            </div>

            <!-- Séptima fila: Notas -->
            <div>
                <flux:textarea label="Notas" size="xs" wire:model="notes" rows="3"
                    placeholder="Notas adicionales sobre la oportunidad">
                </flux:textarea>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                <flux:button icon="x-mark" size="xs" variant="outline" wire:click="closeModal">
                    Cancelar
                </flux:button>
                <flux:button icon="check" size="xs" type="submit">
                    {{ $isEditing ? 'Actualizar' : 'Crear' }} Oportunidad
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
