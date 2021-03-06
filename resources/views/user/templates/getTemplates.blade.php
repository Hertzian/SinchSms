@extends('user.layouts.app')

@section('title')
  <h1>Plantillas</h1>
@endsection

@section('content')
    
  <section class="content">
        
    <div class="row">

      <!-- Contenido agregar plantillas -->
      <div class="col-xl-4 col-md-12 col-12">
        <div class="small-box bg-success">
          <div class="inner">
            <h3>{{ count($templates) }}</h3>
            @if (count($templates) == 1)
              <p>Plantilla</p>                
            @else
              <p>Plantillas</p>
            @endif
          </div>
          <div class="icon">
            <i class="fa fa-copy"></i>
          </div>
          <a href="#add-modal" class="small-box-footer" data-target="#add-modal" data-toggle="modal">Agregar plantilla <i class="fa fa-arrow-right"></i></a>
        </div>
      </div>

      @if (count($templates) >= 1)
        {{-- Modal SMS --}}
        <div class="col-xl-4 col-md-12 col-12">
          <div class="small-box bg-warning">
            <div class="inner">              
              <h3>Mensajes</h3>
              <p>Enviar mensajes</p>
            </div>
            <div class="icon">
              <i class="fas fa-envelope-open-text"></i>
            </div>
            <a href="#send-messages-modal" class="small-box-footer" data-target="#send-messages-modal" data-toggle="modal">Enviar mensajes a lista de contactos <i class="fa fa-arrow-right"></i></a>
          </div>
        </div>
      @endif
      
    </div> 











    <!-- Contenido tabla -->
    <div class="row">
      
      <div class="col-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Lista de plantillas </h3>
          </div>
          <div class="box-body">
            @if (count($templates) >= 1)
            <table id="table_template" class="table table-bordered table-striped table-responsive">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre de plantilla</th>
                  <th>Contenido</th>
                  <th>Creado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>#</th>
                  <th>Nombre de plantilla</th>
                  <th>Contenido</th>
                  <th>Creado</th>
                  <th>Acciones</th>
                </tr>
              </tfoot>
            

            
            @foreach ($templates as $template)                      
              <tr>
                <td>{{ $template->id }}</td>
                <td>{{ $template->name }}</td>
                <td>{{ $template->content }}</td>
                <td>{{ $template->created_at }}</td>
                <td> 

                  {{-- buton group --}}
                  <div class="btn-group">
                    {{-- edit template button --}}
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#edit-modal{{ $template->id }}" href="#edit-modal" >Editar <i class="fa fa-pencil" aria-hidden="true"></i></button>
                    {{-- delete template button --}}
                    <a href="#" class="btn btn-danger" data-target="#deleteTemplate-{{ $template->id }}" data-toggle="modal">Eliminar <span class="pull-right badge bg-danger"><i class="fa fa-remove" aria-hidden="true"></i> </span></a>
                  </div>

                  <!-- edit template modal  -->
                  <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="edit-modal{{ $template->id }}" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title" id="myLargeModalLabel">Editar plantilla</h4>
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body ">
                          <form action="{{ url('/user/edittemplate/' . $template->id) }}" method="post" class="">
                            @csrf
                            <div class="form-group row">
                              <div class="col-2"></div>
                              <label for="recupient-input" class="col-3 col-form-label">Nombre de plantilla</label>
                              <div class="col-xl-4 col-md-6 col-6">
                                <input name="name" class="form-control" type="text" value="{{ $template->name }} " placeholder="Template name" required id="temaplate-name">
                              </div>
                            </div>
                            <div class="form-group row">
                              <div class="col-2"></div>
                                <label for="text-message" class="col-3 col-form-label">Contenido</label>
                                <div class="col-xl-4 col-md-6 col-6">
                                  <textarea name="content" class="form-control" rows="5" placeholder="Enter ..." required id="texto_personalizado" onkeyup="valTextMessage(this);">{{ $template->content }}</textarea><br>
                                  <p id="letters">Message, Characters: 0 </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default "  data-dismiss="modal">Cerrar</button>
                          <button type="submit" class="btn btn-info float-right">Guardar plantilla</button>
                          <button type="button" class="btn btn-warning col-xl-2 col-md-2 col-3 float-right" onclick="limpiar_template();">Limpiar</button>
                        </div>
                          </form>
                      </div>
                    </div>
                  </div>
                  {{-- /Modal *********************************************--}}

                  {{-- Delete template modal --}}
                  <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="deleteTemplate-{{ $template->id }}" aria-labelledby="deleteTemplate" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog modal-lg">
                      <div class="modal-content">

                        <div class="modal-header">
                          <h4 class="modal-title" id="myLargeModalLabel">Eliminar: {{ $template->name }}</h4>
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                          
                        <div class="modal-body ">   
                          <p>¿Estas seguro de querer eliminar la plantilla de forma permanente?</p>
                          <p>Esta acción no se puede deshacer.</p>
                        </div>

                        <div class="modal-footer">
                          <form action="{{ url('/user/deletetemplate/' . $template->id) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger mx-5 float-right" onclick="deleteElement()"><i class="fa fa-remove" aria-hidden="true"></i> Sí, eliminar</button>
                          </form>
                          <button type="button" class="btn btn-default "  data-dismiss="modal">Cerrar</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  {{-- /Modal *********************************************--}}
                    
                </td>
              </tr>
          </div>
            @endforeach
            





            @else
                <div class="text-center">
                  <i class="fa fa-grav font-size-70"></i>
                </div>   
                <div class="text-center">
                  ¡No hay envíos registrados aún!
                </div>
            
            @endif
          </table>
          {{ $templates->onEachSide(1)->links() }}
        </div>
      </div>
    </div>

























<!-- Contenido de Modal agregar plantilla  -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="add-modal" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myLargeModalLabel">Nueva plantilla</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <div class="modal-body ">
        <form action="{{ url('/user/newtemplate') }}" method="post" class="">
          @csrf
          <div class="form-group row">
            <div class="col-2"></div>
            <label for="recupient-input" class="col-3 col-form-label">Nombre de plantilla</label>
            <div class="col-xl-4 col-md-6 col-6">
              <input name="name" class="form-control" type="text" value="" placeholder="Nombre de plantilla" required id="temaplate-name">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-2"></div>
              <label for="text-message" class="col-3 col-form-label">Contenido</label>
              <div class="col-xl-4 col-md-6 col-6">
                <textarea class="form-control" rows="5" placeholder="Escribe tu mensaje" required name="content" id="texto_personalizado" onkeyup="valTextMessage(this);"></textarea><br>
              </div>
          </div>
      </div>
          <div class="form-group row">
            <div class="col-2"></div>
            <label for="" class="col-3">*Para personalizar incluir ${cliente} en el contenido de la plantilla</label>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default"  data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-info float-right" onclick="ok()">Guardar plantilla</button>
            <button type="button" class="btn btn-warning col-xl-2 col-md-2 col-3 float-right" onclick="limpiar_template();">Limpiar</button>
          </div>
        </form>
    </div>
  </div>
</div>
{{-- </div> --}}

      

{{-- Contenido de Modal enviar SMS --}}
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="send-messages-modal" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myLargeModalLabel">Enviar plantilla de mensajes a lista de contactos</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>

      <div class="modal-body ">            
        <form action="{{ url('/user/sendtemplate/' . $account->id) }}" method="post" class="">
          @csrf
          <div class="form-group row">
            <div class="col-2"></div>
            <label for="recupient-input" class="col-3 col-form-label">Nombre de lista de contactos</label>
            <div class="col-xl-4 col-md-6 col-6">                  
              <select class="form-control" name="item_list_id" id="">
                <option value=""></option>
                
                @if (count($batches) >= 1)
                  @foreach ($batches as $batch)
                    <option value="{{ $batch->id }}">{{ $batch->name }}</option>          
                  @endforeach
                @else
                  <option value="">No hay listas registrados aún</option>
                @endif

              </select>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-2"></div>
            <label for="recupient-input" class="col-3 col-form-label">Nombre de plantilla</label>
            <div class="col-xl-4 col-md-6 col-6">                  
              <select class="form-control" name="template_id" id="">
                <option value=""></option>
                
                @if (count($templates) >= 1)
                  @foreach ($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>          
                  @endforeach
                @else
                  <option value="">No hay plantillas aún</option>
                @endif

              </select>
            </div>
          </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default "  data-dismiss="modal">Cerrar</button>
        <button type="submit" class="btn btn-success float-right" onclick="ok()">Enviar</button>
        {{-- <button type="button" class="btn btn-warning col-xl-2 col-md-2 col-3 float-right" onclick="limpiar_template();">Limpiar</button> --}}
      </div>
        </form>

    </div>
  </div>
</div>

      

    
</section>

@endsection