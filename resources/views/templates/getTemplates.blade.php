@extends('layouts.app')

@section('title')
    <h1>Template</h1>
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
            <i class="fa fa-window-maximize"></i>
          </div>
          <a href="#add-modal" class="small-box-footer" data-target="#add-modal" data-toggle="modal">Agregar plantilla <i class="fa fa-arrow-right"></i></a>
        </div>
      </div>

      <!-- Enviar mensajes -->
      <div class="col-xl-4 col-md-12 col-12">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>SMS</h3>
            <p>Enviar mensajes</p>                
          </div>
          <div class="icon">
            <i class="fa fa-window-maximize"></i>
          </div>
          <a href="#add-modal" class="small-box-footer" data-target="#add-modal" data-toggle="modal">Enviar nuevo mensaje con plantilla <i class="fa fa-arrow-right"></i></a>
        </div>
      </div>

    </div> 


    <!-- Contenido tabla -->
    <div class="row">
      
      <div class="col-12">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Lista de plantillas </h3>
          </div>
          <div class="box-body">
            <table id="table_template" class="table table-bordered table-striped table-responsive">
              <thead>
                <tr>
                  <th>Nombre de plantilla</th>
                  <th>Contenido</th>
                  <th>Creado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>Nombre de plantilla</th>
                  <th>Contenido</th>
                  <th>Creado</th>
                  <th>Acciones</th>
                </tr>
              </tfoot>
              <tbody>
                @if (count($templates) >= 1)
                @foreach ($templates as $template)                      
                    <tr>
                      <td>{{ $template->name }}</td>
                      <td>{{ $template->template }}</td>
                      <td>{{ $template->created_at }}</td>
                      <td> 
                        <div class="btn-group">
                          <button type="button" class="btn btn-success" data-toggle="modal" data-target="#edit-modal" href="#edit-modal" >Editar <i class="fa fa-pencil" aria-hidden="true"></i></button>
                          <form action="{{ url('/deletetemplate/' . $template->id) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-danger mx-5" id="sa-warning" onclick="deleteElement()"><i class="fa fa-remove" aria-hidden="true"></i></button>
                          </form>
                          {{-- Modal ************************************** --}}
                          <!-- Contenido de Modal Editar plantilla  -->
                            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="edit-modal" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-lg">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h4 class="modal-title" id="myLargeModalLabel">Editar plantilla</h4>
                                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body ">
                                      <form action="{{ url('/edittemplate/' . $template->id) }}" method="post" class="">
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
                                          <button type="submit" class="btn btn-info float-right" onclick="ok()">Guardar plantilla</button>
                                          <button type="button" class="btn btn-warning col-xl-2 col-md-2 col-3 float-right" onclick="limpiar_template();">Limpiar</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                          {{-- /Modal *********************************************--}}
                          
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  @else
                    <td>No hay plantillas registradas aún :(</td>
                    <td></td>
                    <td></td>
                    <td></td>
                  @endif
                  
                </tr>
              </tbody>
            </table>
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
            <form action="{{ url('/newtemplate') }}" method="post" class="">
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
                    <textarea name="content" class="form-control" rows="5" placeholder="Escribe el contenido..." required id="texto_personalizado" onkeyup="valTextMessage(this);"></textarea><br>
                    <p id="letters">Message, Characters: 0 </p>
                  </div>
                </div>
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
      </div>

      <!-- Contenido de Modal Editar plantilla  -->
      <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="edit-modal" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="myLargeModalLabel">New template</h4>
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body ">
              <form action="" method="post" class="">
                <div class="form-group row">
                  <div class="col-2"></div>
                  <label for="recupient-input" class="col-3 col-form-label">Template name</label>
                  <div class="col-xl-4 col-md-6 col-6">
                    <input class="form-control" type="text" value="" placeholder="Template name" required id="temaplate-name">
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-2"></div>
                    <label for="text-message" class="col-3 col-form-label">Text</label>
                    <div class="col-xl-4 col-md-6 col-6">
                      <textarea class="form-control" rows="5" placeholder="Enter ..." required id="texto_personalizado" onkeyup="valTextMessage(this);"></textarea><br>
                      <p id="letters">Message, Characters: 0 </p>
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default "  data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info float-right" onclick="ok()">Save template</button>
                <button type="button" class="btn btn-warning col-xl-2 col-md-2 col-3 float-right" onclick="limpiar_template();">Limpiar</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      

    
  </section>

@endsection